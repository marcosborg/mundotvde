<?php

namespace Tests\Feature;

use App\Models\Inspection;
use App\Services\Inspections\InspectionSequenceService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InspectionSequenceTest extends TestCase
{
    /** @dataProvider transitions */
    public function test_vehicle_inspection_transitions(?string $previousType, string $nextType, bool $allowed): void
    {
        $previous = $previousType === null ? null : new Inspection(['type' => $previousType]);
        $sequence = $this->getMockBuilder(InspectionSequenceService::class)
            ->onlyMethods(['resolvePreviousInspection'])->getMock();
        $sequence->expects($this->once())->method('resolvePreviousInspection')
            ->with(42)->willReturn($previous);

        if (!$allowed) {
            $this->expectException(ValidationException::class);
        }

        $this->assertSame($previous, $sequence->validateCreationSequence($nextType, 42));
    }

    public static function transitions(): array
    {
        return [
            'collect directly after delivery' => ['handover', 'return', true],
            'optional routine after delivery' => ['handover', 'routine', true],
            'repeated routine while in use' => ['routine', 'routine', true],
            'collect after routine' => ['routine', 'return', true],
            'reassign after collection' => ['return', 'handover', true],
            'exit after collection' => ['return', 'fleet_exit', true],
            'initial inspection required' => [null, 'return', false],
            'new vehicle starts with initial' => [null, 'initial', true],
            'cannot collect before delivery' => ['initial', 'return', false],
            'cannot exit before collection' => ['handover', 'fleet_exit', false],
            'cannot deliver again before collection' => ['handover', 'handover', false],
        ];
    }
}
