<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 18,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 19,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 20,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 21,
                'title' => 'faq_management_access',
            ],
            [
                'id'    => 22,
                'title' => 'faq_category_create',
            ],
            [
                'id'    => 23,
                'title' => 'faq_category_edit',
            ],
            [
                'id'    => 24,
                'title' => 'faq_category_show',
            ],
            [
                'id'    => 25,
                'title' => 'faq_category_delete',
            ],
            [
                'id'    => 26,
                'title' => 'faq_category_access',
            ],
            [
                'id'    => 27,
                'title' => 'faq_question_create',
            ],
            [
                'id'    => 28,
                'title' => 'faq_question_edit',
            ],
            [
                'id'    => 29,
                'title' => 'faq_question_show',
            ],
            [
                'id'    => 30,
                'title' => 'faq_question_delete',
            ],
            [
                'id'    => 31,
                'title' => 'faq_question_access',
            ],
            [
                'id'    => 32,
                'title' => 'car_rental_access',
            ],
            [
                'id'    => 33,
                'title' => 'car_create',
            ],
            [
                'id'    => 34,
                'title' => 'car_edit',
            ],
            [
                'id'    => 35,
                'title' => 'car_show',
            ],
            [
                'id'    => 36,
                'title' => 'car_delete',
            ],
            [
                'id'    => 37,
                'title' => 'car_access',
            ],
            [
                'id'    => 38,
                'title' => 'car_rental_contact_request_create',
            ],
            [
                'id'    => 39,
                'title' => 'car_rental_contact_request_edit',
            ],
            [
                'id'    => 40,
                'title' => 'car_rental_contact_request_show',
            ],
            [
                'id'    => 41,
                'title' => 'car_rental_contact_request_delete',
            ],
            [
                'id'    => 42,
                'title' => 'car_rental_contact_request_access',
            ],
            [
                'id'    => 43,
                'title' => 'home_page_access',
            ],
            [
                'id'    => 44,
                'title' => 'hero_banner_create',
            ],
            [
                'id'    => 45,
                'title' => 'hero_banner_edit',
            ],
            [
                'id'    => 46,
                'title' => 'hero_banner_show',
            ],
            [
                'id'    => 47,
                'title' => 'hero_banner_delete',
            ],
            [
                'id'    => 48,
                'title' => 'hero_banner_access',
            ],
            [
                'id'    => 49,
                'title' => 'home_info_create',
            ],
            [
                'id'    => 50,
                'title' => 'home_info_edit',
            ],
            [
                'id'    => 51,
                'title' => 'home_info_show',
            ],
            [
                'id'    => 52,
                'title' => 'home_info_delete',
            ],
            [
                'id'    => 53,
                'title' => 'home_info_access',
            ],
            [
                'id'    => 54,
                'title' => 'activity_create',
            ],
            [
                'id'    => 55,
                'title' => 'activity_edit',
            ],
            [
                'id'    => 56,
                'title' => 'activity_show',
            ],
            [
                'id'    => 57,
                'title' => 'activity_delete',
            ],
            [
                'id'    => 58,
                'title' => 'activity_access',
            ],
            [
                'id'    => 59,
                'title' => 'testimonial_create',
            ],
            [
                'id'    => 60,
                'title' => 'testimonial_edit',
            ],
            [
                'id'    => 61,
                'title' => 'testimonial_show',
            ],
            [
                'id'    => 62,
                'title' => 'testimonial_delete',
            ],
            [
                'id'    => 63,
                'title' => 'testimonial_access',
            ],
            [
                'id'    => 64,
                'title' => 'menu_own_car_access',
            ],
            [
                'id'    => 65,
                'title' => 'own_car_create',
            ],
            [
                'id'    => 66,
                'title' => 'own_car_edit',
            ],
            [
                'id'    => 67,
                'title' => 'own_car_show',
            ],
            [
                'id'    => 68,
                'title' => 'own_car_delete',
            ],
            [
                'id'    => 69,
                'title' => 'own_car_access',
            ],
            [
                'id'    => 70,
                'title' => 'own_car_form_create',
            ],
            [
                'id'    => 71,
                'title' => 'own_car_form_edit',
            ],
            [
                'id'    => 72,
                'title' => 'own_car_form_show',
            ],
            [
                'id'    => 73,
                'title' => 'own_car_form_delete',
            ],
            [
                'id'    => 74,
                'title' => 'own_car_form_access',
            ],
            [
                'id'    => 75,
                'title' => 'menu_stand_access',
            ],
            [
                'id'    => 76,
                'title' => 'fuel_create',
            ],
            [
                'id'    => 77,
                'title' => 'fuel_edit',
            ],
            [
                'id'    => 78,
                'title' => 'fuel_show',
            ],
            [
                'id'    => 79,
                'title' => 'fuel_delete',
            ],
            [
                'id'    => 80,
                'title' => 'fuel_access',
            ],
            [
                'id'    => 81,
                'title' => 'month_create',
            ],
            [
                'id'    => 82,
                'title' => 'month_edit',
            ],
            [
                'id'    => 83,
                'title' => 'month_show',
            ],
            [
                'id'    => 84,
                'title' => 'month_delete',
            ],
            [
                'id'    => 85,
                'title' => 'month_access',
            ],
            [
                'id'    => 86,
                'title' => 'origin_create',
            ],
            [
                'id'    => 87,
                'title' => 'origin_edit',
            ],
            [
                'id'    => 88,
                'title' => 'origin_show',
            ],
            [
                'id'    => 89,
                'title' => 'origin_delete',
            ],
            [
                'id'    => 90,
                'title' => 'origin_access',
            ],
            [
                'id'    => 91,
                'title' => 'stand_item_access',
            ],
            [
                'id'    => 92,
                'title' => 'stand_car_create',
            ],
            [
                'id'    => 93,
                'title' => 'stand_car_edit',
            ],
            [
                'id'    => 94,
                'title' => 'stand_car_show',
            ],
            [
                'id'    => 95,
                'title' => 'stand_car_delete',
            ],
            [
                'id'    => 96,
                'title' => 'stand_car_access',
            ],
            [
                'id'    => 97,
                'title' => 'status_create',
            ],
            [
                'id'    => 98,
                'title' => 'status_edit',
            ],
            [
                'id'    => 99,
                'title' => 'status_show',
            ],
            [
                'id'    => 100,
                'title' => 'status_delete',
            ],
            [
                'id'    => 101,
                'title' => 'status_access',
            ],
            [
                'id'    => 102,
                'title' => 'menu_courier_access',
            ],
            [
                'id'    => 103,
                'title' => 'courier_create',
            ],
            [
                'id'    => 104,
                'title' => 'courier_edit',
            ],
            [
                'id'    => 105,
                'title' => 'courier_show',
            ],
            [
                'id'    => 106,
                'title' => 'courier_delete',
            ],
            [
                'id'    => 107,
                'title' => 'courier_access',
            ],
            [
                'id'    => 108,
                'title' => 'courier_form_create',
            ],
            [
                'id'    => 109,
                'title' => 'courier_form_edit',
            ],
            [
                'id'    => 110,
                'title' => 'courier_form_show',
            ],
            [
                'id'    => 111,
                'title' => 'courier_form_delete',
            ],
            [
                'id'    => 112,
                'title' => 'courier_form_access',
            ],
            [
                'id'    => 113,
                'title' => 'menu_training_access',
            ],
            [
                'id'    => 114,
                'title' => 'training_create',
            ],
            [
                'id'    => 115,
                'title' => 'training_edit',
            ],
            [
                'id'    => 116,
                'title' => 'training_show',
            ],
            [
                'id'    => 117,
                'title' => 'training_delete',
            ],
            [
                'id'    => 118,
                'title' => 'training_access',
            ],
            [
                'id'    => 119,
                'title' => 'training_form_create',
            ],
            [
                'id'    => 120,
                'title' => 'training_form_edit',
            ],
            [
                'id'    => 121,
                'title' => 'training_form_show',
            ],
            [
                'id'    => 122,
                'title' => 'training_form_delete',
            ],
            [
                'id'    => 123,
                'title' => 'training_form_access',
            ],
            [
                'id'    => 124,
                'title' => 'product_management_access',
            ],
            [
                'id'    => 125,
                'title' => 'product_category_create',
            ],
            [
                'id'    => 126,
                'title' => 'product_category_edit',
            ],
            [
                'id'    => 127,
                'title' => 'product_category_show',
            ],
            [
                'id'    => 128,
                'title' => 'product_category_delete',
            ],
            [
                'id'    => 129,
                'title' => 'product_category_access',
            ],
            [
                'id'    => 130,
                'title' => 'product_tag_create',
            ],
            [
                'id'    => 131,
                'title' => 'product_tag_edit',
            ],
            [
                'id'    => 132,
                'title' => 'product_tag_show',
            ],
            [
                'id'    => 133,
                'title' => 'product_tag_delete',
            ],
            [
                'id'    => 134,
                'title' => 'product_tag_access',
            ],
            [
                'id'    => 135,
                'title' => 'product_create',
            ],
            [
                'id'    => 136,
                'title' => 'product_edit',
            ],
            [
                'id'    => 137,
                'title' => 'product_show',
            ],
            [
                'id'    => 138,
                'title' => 'product_delete',
            ],
            [
                'id'    => 139,
                'title' => 'product_access',
            ],
            [
                'id'    => 140,
                'title' => 'menu_tranfer_tour_access',
            ],
            [
                'id'    => 141,
                'title' => 'transfer_category_create',
            ],
            [
                'id'    => 142,
                'title' => 'transfer_category_edit',
            ],
            [
                'id'    => 143,
                'title' => 'transfer_category_show',
            ],
            [
                'id'    => 144,
                'title' => 'transfer_category_delete',
            ],
            [
                'id'    => 145,
                'title' => 'transfer_category_access',
            ],
            [
                'id'    => 146,
                'title' => 'transfer_tag_create',
            ],
            [
                'id'    => 147,
                'title' => 'transfer_tag_edit',
            ],
            [
                'id'    => 148,
                'title' => 'transfer_tag_show',
            ],
            [
                'id'    => 149,
                'title' => 'transfer_tag_delete',
            ],
            [
                'id'    => 150,
                'title' => 'transfer_tag_access',
            ],
            [
                'id'    => 151,
                'title' => 'tranfer_tour_create',
            ],
            [
                'id'    => 152,
                'title' => 'tranfer_tour_edit',
            ],
            [
                'id'    => 153,
                'title' => 'tranfer_tour_show',
            ],
            [
                'id'    => 154,
                'title' => 'tranfer_tour_delete',
            ],
            [
                'id'    => 155,
                'title' => 'tranfer_tour_access',
            ],
            [
                'id'    => 156,
                'title' => 'product_form_create',
            ],
            [
                'id'    => 157,
                'title' => 'product_form_edit',
            ],
            [
                'id'    => 158,
                'title' => 'product_form_show',
            ],
            [
                'id'    => 159,
                'title' => 'product_form_delete',
            ],
            [
                'id'    => 160,
                'title' => 'product_form_access',
            ],
            [
                'id'    => 161,
                'title' => 'transfer_form_create',
            ],
            [
                'id'    => 162,
                'title' => 'transfer_form_edit',
            ],
            [
                'id'    => 163,
                'title' => 'transfer_form_show',
            ],
            [
                'id'    => 164,
                'title' => 'transfer_form_delete',
            ],
            [
                'id'    => 165,
                'title' => 'transfer_form_access',
            ],
            [
                'id'    => 166,
                'title' => 'menu_consulting_access',
            ],
            [
                'id'    => 167,
                'title' => 'consulting_create',
            ],
            [
                'id'    => 168,
                'title' => 'consulting_edit',
            ],
            [
                'id'    => 169,
                'title' => 'consulting_show',
            ],
            [
                'id'    => 170,
                'title' => 'consulting_delete',
            ],
            [
                'id'    => 171,
                'title' => 'consulting_access',
            ],
            [
                'id'    => 172,
                'title' => 'consulting_form_create',
            ],
            [
                'id'    => 173,
                'title' => 'consulting_form_edit',
            ],
            [
                'id'    => 174,
                'title' => 'consulting_form_show',
            ],
            [
                'id'    => 175,
                'title' => 'consulting_form_delete',
            ],
            [
                'id'    => 176,
                'title' => 'consulting_form_access',
            ],
            [
                'id'    => 177,
                'title' => 'newsletter_create',
            ],
            [
                'id'    => 178,
                'title' => 'newsletter_edit',
            ],
            [
                'id'    => 179,
                'title' => 'newsletter_show',
            ],
            [
                'id'    => 180,
                'title' => 'newsletter_delete',
            ],
            [
                'id'    => 181,
                'title' => 'newsletter_access',
            ],
            [
                'id'    => 182,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
