<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/css/perfect-scrollbar.min.css" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @guest
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('frontend.home') }}">
                                    {{ __('Dashboard') }}
                                </a>
                            </li>
                        @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if(Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('frontend.profile.index') }}">{{ __('My profile') }}</a>

                                    @can('user_management_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.userManagement.title') }}
                                        </a>
                                    @endcan
                                    @can('permission_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.permissions.index') }}">
                                            {{ trans('cruds.permission.title') }}
                                        </a>
                                    @endcan
                                    @can('role_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.roles.index') }}">
                                            {{ trans('cruds.role.title') }}
                                        </a>
                                    @endcan
                                    @can('user_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.users.index') }}">
                                            {{ trans('cruds.user.title') }}
                                        </a>
                                    @endcan
                                    @can('company_invoice_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.company-invoices.index') }}">
                                            {{ trans('cruds.companyInvoice.title') }}
                                        </a>
                                    @endcan
                                    @can('tvde_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.tvde.title') }}
                                        </a>
                                    @endcan
                                    @can('driver_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.drivers.index') }}">
                                            {{ trans('cruds.driver.title') }}
                                        </a>
                                    @endcan
                                    @can('tvde_config_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.tvdeConfig.title') }}
                                        </a>
                                    @endcan
                                    @can('card_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.cards.index') }}">
                                            {{ trans('cruds.card.title') }}
                                        </a>
                                    @endcan
                                    @can('electric_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.electrics.index') }}">
                                            {{ trans('cruds.electric.title') }}
                                        </a>
                                    @endcan
                                    @can('toll_card_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.toll-cards.index') }}">
                                            {{ trans('cruds.tollCard.title') }}
                                        </a>
                                    @endcan
                                    @can('local_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.locals.index') }}">
                                            {{ trans('cruds.local.title') }}
                                        </a>
                                    @endcan
                                    @can('state_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.states.index') }}">
                                            {{ trans('cruds.state.title') }}
                                        </a>
                                    @endcan
                                    @can('tvde_operator_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.tvde-operators.index') }}">
                                            {{ trans('cruds.tvdeOperator.title') }}
                                        </a>
                                    @endcan
                                    @can('contract_type_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.contract-types.index') }}">
                                            {{ trans('cruds.contractType.title') }}
                                        </a>
                                    @endcan
                                    @can('contract_type_rank_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.contract-type-ranks.index') }}">
                                            {{ trans('cruds.contractTypeRank.title') }}
                                        </a>
                                    @endcan
                                    @can('contract_vat_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.contract-vats.index') }}">
                                            {{ trans('cruds.contractVat.title') }}
                                        </a>
                                    @endcan
                                    @can('document_warning_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.document-warnings.index') }}">
                                            {{ trans('cruds.documentWarning.title') }}
                                        </a>
                                    @endcan
                                    @can('activity_management_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.activityManagement.title') }}
                                        </a>
                                    @endcan
                                    @can('tvde_year_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.tvde-years.index') }}">
                                            {{ trans('cruds.tvdeYear.title') }}
                                        </a>
                                    @endcan
                                    @can('tvde_month_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.tvde-months.index') }}">
                                            {{ trans('cruds.tvdeMonth.title') }}
                                        </a>
                                    @endcan
                                    @can('tvde_week_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.tvde-weeks.index') }}">
                                            {{ trans('cruds.tvdeWeek.title') }}
                                        </a>
                                    @endcan
                                    @can('activity_launch_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.activity-launches.index') }}">
                                            {{ trans('cruds.activityLaunch.title') }}
                                        </a>
                                    @endcan
                                    @can('activity_per_operator_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.activity-per-operators.index') }}">
                                            {{ trans('cruds.activityPerOperator.title') }}
                                        </a>
                                    @endcan
                                    @can('periods_of_the_year_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.periods-of-the-years.index') }}">
                                            {{ trans('cruds.periodsOfTheYear.title') }}
                                        </a>
                                    @endcan
                                    @can('document_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.documents.index') }}">
                                            {{ trans('cruds.document.title') }}
                                        </a>
                                    @endcan
                                    @can('receipt_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.receipts.index') }}">
                                            {{ trans('cruds.receipt.title') }}
                                        </a>
                                    @endcan
                                    @can('tvde_activity_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.tvde-activities.index') }}">
                                            {{ trans('cruds.tvdeActivity.title') }}
                                        </a>
                                    @endcan
                                    @can('combustion_transaction_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.combustion-transactions.index') }}">
                                            {{ trans('cruds.combustionTransaction.title') }}
                                        </a>
                                    @endcan
                                    @can('electric_transaction_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.electric-transactions.index') }}">
                                            {{ trans('cruds.electricTransaction.title') }}
                                        </a>
                                    @endcan
                                    @can('toll_payment_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.toll-payments.index') }}">
                                            {{ trans('cruds.tollPayment.title') }}
                                        </a>
                                    @endcan
                                    @can('adjustment_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.adjustments.index') }}">
                                            {{ trans('cruds.adjustment.title') }}
                                        </a>
                                    @endcan
                                    @can('team_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.teams.index') }}">
                                            {{ trans('cruds.team.title') }}
                                        </a>
                                    @endcan
                                    @can('weekly_vehicle_expense_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.weekly-vehicle-expenses.index') }}">
                                            {{ trans('cruds.weeklyVehicleExpense.title') }}
                                        </a>
                                    @endcan
                                    @can('car_track_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.car-tracks.index') }}">
                                            {{ trans('cruds.carTrack.title') }}
                                        </a>
                                    @endcan
                                    @can('car_hire_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.car-hires.index') }}">
                                            {{ trans('cruds.carHire.title') }}
                                        </a>
                                    @endcan
                                    @can('recorded_log_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.recorded-logs.index') }}">
                                            {{ trans('cruds.recordedLog.title') }}
                                        </a>
                                    @endcan
                                    @can('expense_receipt_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.expense-receipts.index') }}">
                                            {{ trans('cruds.expenseReceipt.title') }}
                                        </a>
                                    @endcan
                                    @can('user_alert_access')
                                        <a class="dropdown-item" href="{{ route('frontend.user-alerts.index') }}">
                                            {{ trans('cruds.userAlert.title') }}
                                        </a>
                                    @endcan
                                    @can('faq_management_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.faqManagement.title') }}
                                        </a>
                                    @endcan
                                    @can('faq_category_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.faq-categories.index') }}">
                                            {{ trans('cruds.faqCategory.title') }}
                                        </a>
                                    @endcan
                                    @can('faq_question_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.faq-questions.index') }}">
                                            {{ trans('cruds.faqQuestion.title') }}
                                        </a>
                                    @endcan
                                    @can('car_rental_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.carRental.title') }}
                                        </a>
                                    @endcan
                                    @can('car_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.cars.index') }}">
                                            {{ trans('cruds.car.title') }}
                                        </a>
                                    @endcan
                                    @can('car_rental_contact_request_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.car-rental-contact-requests.index') }}">
                                            {{ trans('cruds.carRentalContactRequest.title') }}
                                        </a>
                                    @endcan
                                    @can('website_configuration_access')
                                        <a class="dropdown-item" href="{{ route('frontend.website-configurations.index') }}">
                                            {{ trans('cruds.websiteConfiguration.title') }}
                                        </a>
                                    @endcan
                                    @can('home_page_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.homePage.title') }}
                                        </a>
                                    @endcan
                                    @can('hero_banner_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.hero-banners.index') }}">
                                            {{ trans('cruds.heroBanner.title') }}
                                        </a>
                                    @endcan
                                    @can('service_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.services.index') }}">
                                            {{ trans('cruds.service.title') }}
                                        </a>
                                    @endcan
                                    @can('testimonial_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.testimonials.index') }}">
                                            {{ trans('cruds.testimonial.title') }}
                                        </a>
                                    @endcan
                                    @can('page_menu_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.pageMenu.title') }}
                                        </a>
                                    @endcan
                                    @can('page_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.pages.index') }}">
                                            {{ trans('cruds.page.title') }}
                                        </a>
                                    @endcan
                                    @can('menu_own_car_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.menuOwnCar.title') }}
                                        </a>
                                    @endcan
                                    @can('own_car_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.own-cars.index') }}">
                                            {{ trans('cruds.ownCar.title') }}
                                        </a>
                                    @endcan
                                    @can('own_car_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.own-car-forms.index') }}">
                                            {{ trans('cruds.ownCarForm.title') }}
                                        </a>
                                    @endcan
                                    @can('menu_stand_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.menuStand.title') }}
                                        </a>
                                    @endcan
                                    @can('stand_item_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.standItem.title') }}
                                        </a>
                                    @endcan
                                    @can('fuel_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.fuels.index') }}">
                                            {{ trans('cruds.fuel.title') }}
                                        </a>
                                    @endcan
                                    @can('month_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.months.index') }}">
                                            {{ trans('cruds.month.title') }}
                                        </a>
                                    @endcan
                                    @can('origin_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.origins.index') }}">
                                            {{ trans('cruds.origin.title') }}
                                        </a>
                                    @endcan
                                    @can('status_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.statuses.index') }}">
                                            {{ trans('cruds.status.title') }}
                                        </a>
                                    @endcan
                                    @can('brand_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.brands.index') }}">
                                            {{ trans('cruds.brand.title') }}
                                        </a>
                                    @endcan
                                    @can('car_model_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.car-models.index') }}">
                                            {{ trans('cruds.carModel.title') }}
                                        </a>
                                    @endcan
                                    @can('transmission_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.transmissions.index') }}">
                                            {{ trans('cruds.transmission.title') }}
                                        </a>
                                    @endcan
                                    @can('stand_car_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.stand-cars.index') }}">
                                            {{ trans('cruds.standCar.title') }}
                                        </a>
                                    @endcan
                                    @can('stand_car_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.stand-car-forms.index') }}">
                                            {{ trans('cruds.standCarForm.title') }}
                                        </a>
                                    @endcan
                                    @can('menu_courier_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.menuCourier.title') }}
                                        </a>
                                    @endcan
                                    @can('courier_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.couriers.index') }}">
                                            {{ trans('cruds.courier.title') }}
                                        </a>
                                    @endcan
                                    @can('courier_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.courier-forms.index') }}">
                                            {{ trans('cruds.courierForm.title') }}
                                        </a>
                                    @endcan
                                    @can('menu_training_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.menuTraining.title') }}
                                        </a>
                                    @endcan
                                    @can('training_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.trainings.index') }}">
                                            {{ trans('cruds.training.title') }}
                                        </a>
                                    @endcan
                                    @can('training_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.training-forms.index') }}">
                                            {{ trans('cruds.trainingForm.title') }}
                                        </a>
                                    @endcan
                                    @can('product_management_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.productManagement.title') }}
                                        </a>
                                    @endcan
                                    @can('product_category_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.product-categories.index') }}">
                                            {{ trans('cruds.productCategory.title') }}
                                        </a>
                                    @endcan
                                    @can('product_tag_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.product-tags.index') }}">
                                            {{ trans('cruds.productTag.title') }}
                                        </a>
                                    @endcan
                                    @can('product_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.products.index') }}">
                                            {{ trans('cruds.product.title') }}
                                        </a>
                                    @endcan
                                    @can('product_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.product-forms.index') }}">
                                            {{ trans('cruds.productForm.title') }}
                                        </a>
                                    @endcan
                                    @can('menu_tranfer_tour_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.menuTranferTour.title') }}
                                        </a>
                                    @endcan
                                    @can('transfer_tour_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.transfer-tours.index') }}">
                                            {{ trans('cruds.transferTour.title') }}
                                        </a>
                                    @endcan
                                    @can('transfer_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.transfer-forms.index') }}">
                                            {{ trans('cruds.transferForm.title') }}
                                        </a>
                                    @endcan
                                    @can('menu_consulting_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.menuConsulting.title') }}
                                        </a>
                                    @endcan
                                    @can('consulting_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.consultings.index') }}">
                                            {{ trans('cruds.consulting.title') }}
                                        </a>
                                    @endcan
                                    @can('consulting_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.consulting-forms.index') }}">
                                            {{ trans('cruds.consultingForm.title') }}
                                        </a>
                                    @endcan
                                    @can('newsletter_access')
                                        <a class="dropdown-item" href="{{ route('frontend.newsletters.index') }}">
                                            {{ trans('cruds.newsletter.title') }}
                                        </a>
                                    @endcan
                                    @can('legal_access')
                                        <a class="dropdown-item" href="{{ route('frontend.legals.index') }}">
                                            {{ trans('cruds.legal.title') }}
                                        </a>
                                    @endcan
                                    @can('company_document_access')
                                        <a class="dropdown-item" href="{{ route('frontend.company-documents.index') }}">
                                            {{ trans('cruds.companyDocument.title') }}
                                        </a>
                                    @endcan
                                    @can('contracts_menu_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.contractsMenu.title') }}
                                        </a>
                                    @endcan
                                    @can('admin_statement_responsibility_access')
                                        <a class="dropdown-item" href="{{ route('frontend.admin-statement-responsibilities.index') }}">
                                            {{ trans('cruds.adminStatementResponsibility.title') }}
                                        </a>
                                    @endcan
                                    @can('admin_contract_access')
                                        <a class="dropdown-item" href="{{ route('frontend.admin-contracts.index') }}">
                                            {{ trans('cruds.adminContract.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.vehicle.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_setting_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.vehicleSetting.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_brand_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-brands.index') }}">
                                            {{ trans('cruds.vehicleBrand.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_model_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-models.index') }}">
                                            {{ trans('cruds.vehicleModel.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_event_type_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-event-types.index') }}">
                                            {{ trans('cruds.vehicleEventType.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_event_warning_time_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-event-warning-times.index') }}">
                                            {{ trans('cruds.vehicleEventWarningTime.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_event_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-events.index') }}">
                                            {{ trans('cruds.vehicleEvent.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_item_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-items.index') }}">
                                            {{ trans('cruds.vehicleItem.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_expense_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-expenses.index') }}">
                                            {{ trans('cruds.vehicleExpense.title') }}
                                        </a>
                                    @endcan
                                    @can('vehicle_usage_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.vehicle-usages.index') }}">
                                            {{ trans('cruds.vehicleUsage.title') }}
                                        </a>
                                    @endcan
                                    @can('company_access')
                                        <a class="dropdown-item" href="{{ route('frontend.companies.index') }}">
                                            {{ trans('cruds.company.title') }}
                                        </a>
                                    @endcan
                                    @can('company_expenses_menu_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.companyExpensesMenu.title') }}
                                        </a>
                                    @endcan
                                    @can('company_expense_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.company-expenses.index') }}">
                                            {{ trans('cruds.companyExpense.title') }}
                                        </a>
                                    @endcan
                                    @can('company_park_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.company-parks.index') }}">
                                            {{ trans('cruds.companyPark.title') }}
                                        </a>
                                    @endcan
                                    @can('current_account_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.current-accounts.index') }}">
                                            {{ trans('cruds.currentAccount.title') }}
                                        </a>
                                    @endcan
                                    @can('drivers_balance_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.drivers-balances.index') }}">
                                            {{ trans('cruds.driversBalance.title') }}
                                        </a>
                                    @endcan
                                    @can('company_data_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.company-datas.index') }}">
                                            {{ trans('cruds.companyData.title') }}
                                        </a>
                                    @endcan
                                    @can('form_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.form.title') }}
                                        </a>
                                    @endcan
                                    @can('form_name_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.form-names.index') }}">
                                            {{ trans('cruds.formName.title') }}
                                        </a>
                                    @endcan
                                    @can('form_input_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.form-inputs.index') }}">
                                            {{ trans('cruds.formInput.title') }}
                                        </a>
                                    @endcan
                                    @can('form_data_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.form-datas.index') }}">
                                            {{ trans('cruds.formData.title') }}
                                        </a>
                                    @endcan
                                    @can('registo_entrada_veiculo_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.registo-entrada-veiculos.index') }}">
                                            {{ trans('cruds.registoEntradaVeiculo.title') }}
                                        </a>
                                    @endcan
                                    @can('recruitment_form_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.recruitment-forms.index') }}">
                                            {{ trans('cruds.recruitmentForm.title') }}
                                        </a>
                                    @endcan
                                    @can('driver_recommendation_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.driverRecommendation.title') }}
                                        </a>
                                    @endcan
                                    @can('recommendation_status_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.recommendation-statuses.index') }}">
                                            {{ trans('cruds.recommendationStatus.title') }}
                                        </a>
                                    @endcan
                                    @can('recommendation_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.recommendations.index') }}">
                                            {{ trans('cruds.recommendation.title') }}
                                        </a>
                                    @endcan
                                    @can('notification_system_menu_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.notificationSystemMenu.title') }}
                                        </a>
                                    @endcan
                                    @can('notification_system_template_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.notification-system-templates.index') }}">
                                            {{ trans('cruds.notificationSystemTemplate.title') }}
                                        </a>
                                    @endcan
                                    @can('notification_system_message_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.notification-system-messages.index') }}">
                                            {{ trans('cruds.notificationSystemMessage.title') }}
                                        </a>
                                    @endcan
                                    @can('time_log_access')
                                        <a class="dropdown-item" href="{{ route('frontend.time-logs.index') }}">
                                            {{ trans('cruds.timeLog.title') }}
                                        </a>
                                    @endcan
                                    @can('website_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.website.title') }}
                                        </a>
                                    @endcan
                                    @can('news_access')
                                        <a class="dropdown-item ml-3" href="{{ route('frontend.newss.index') }}">
                                            {{ trans('cruds.news.title') }}
                                        </a>
                                    @endcan

                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if(session('message'))
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                        </div>
                    </div>
                </div>
            @endif
            @if($errors->count() > 0)
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <ul class="list-unstyled mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/perfect-scrollbar.min.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
@yield('scripts')

</html>