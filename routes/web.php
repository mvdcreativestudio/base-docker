<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\{
    DashboardController,
    AccountingController,
    CartController,
    CheckoutController,
    ClientController,
    CrmController,
    EcommerceController,
    InvoiceController,
    OmnichannelController,
    OrderController,
    ProductCategoryController,
    ProductController,
    RawMaterialController,
    RoleController,
    StoreController,
    SupplierController,
    SupplierOrderController,
    WhatsAppController,
    CouponController,
    CompanySettingsController,
    DatacenterController,
    MercadoPagoController,
    EmailTemplateController,
    NotificationController,
    OrderPdfController,
    ProductionController,
    CashRegisterController,
    CashRegisterLogController,
    PosOrderController,
    UserController,

};

// Middleware de autenticación y verificación de email
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Data Tables
    Route::get('/clients/datatable', [ClientController::class, 'datatable'])->name('clients.datatable');
    Route::get('/products/datatable', [ProductController::class, 'datatable'])->name('products.datatable');
    Route::get('/product-categories/datatable', [ProductCategoryController::class, 'datatable'])->name('product-categories.datatable');
    Route::get('/orders/datatable', [OrderController::class, 'datatable'])->name('orders.datatable');
    Route::get('/orders/{order}/datatable', [OrderController::class, 'orderProductsDatatable'])->name('order-products.datatable');
    Route::get('/marketing/coupons/datatable', [CouponController::class, 'datatable'])->name('coupons.datatable');
    Route::get('/products/flavors/datatable', [ProductController::class, 'flavorsDatatable'])->name('products.flavors.datatable');
    Route::get('/productions/datatable', [ProductionController::class, 'datatable'])->name('productions.datatable');
    Route::get('users/datatable', [UserController::class, 'datatable'])->name('users.datatable');



    // Recursos con acceso autenticado
    Route::resources([
        'stores' => StoreController::class,
        'roles' => RoleController::class,
        'users' => UserController::class,
        'raw-materials' => RawMaterialController::class,
        'suppliers' => SupplierController::class,
        'supplier-orders' => SupplierOrderController::class,
        'clients' => ClientController::class,
        'products' => ProductController::class,
        'product-categories' => ProductCategoryController::class,
        'orders' => OrderController::class,
        'invoices' => InvoiceController::class,
        'marketing/coupons' => CouponController::class,
        'company-settings' => CompanySettingsController::class,
        'clients' => ClientController::class,
        'productions' => ProductionController::class,
        'points-of-sales' => CashRegisterController::class,
        'pos-orders' => PosOrderController::class,
    ]);
    
    Route::get('/point-of-sale/stores', [CashRegisterController::class, 'storesForCashRegister']);

    // Point of service

    Route::post('/pdv/open', [CashRegisterLogController::class, 'store']);
    Route::post('/pdv/close/{id}', [CashRegisterLogController::class, 'closeCashRegister']);
    Route::get('/pdv/clients/json', [CashRegisterLogController::class, 'getAllClients']);


    // Point of service

    Route::get('/pdv', [CashRegisterLogController::class, 'index'])->middleware('check.open.cash.register')->name('pdv.index');
    Route::get('/pdv/front', [CashRegisterLogController::class, 'front'])->middleware('check.open.cash.register')->name('pdv.front');
    Route::get('/pdv/front2', [CashRegisterLogController::class, 'front2'])->middleware('check.open.cash.register')->name('pdv.front2');


    // Productos para caja registradora
    Route::get('/pdv/products/{id}', [CashRegisterLogController::class, 'getProductsByCashRegister']);
    Route::get('/pdv/flavors', [CashRegisterLogController::class, 'getFlavorsForCashRegister']);
    Route::get('/pdv/categories', [CashRegisterLogController::class, 'getFathersCategories']);
    Route::post('/pdv/client', [CashRegisterLogController::class, 'storeClient']);
    Route::get('/pdv/log/{id}', [CashRegisterLogController::class, 'getCashRegisterLog']);

    Route::get('/pdv/product-categories', [CashRegisterLogController::class, 'getCategories']);
    Route::post('/pdv/cart', [CashRegisterLogController::class, 'saveCart']);
    Route::get('/pdv/cart', [CashRegisterLogController::class, 'getCart']);
    Route::post('/pdv/client-session', [CashRegisterLogController::class, 'saveClient']);
    Route::get('/pdv/client-session', [CashRegisterLogController::class, 'getClient']);


    // Datacenter
    Route::get('/datacenter-sales', [DatacenterController::class, 'sales'])->name('datacenter.sales');
    Route::get('/api/monthly-income', [DatacenterController::class, 'monthlyIncome']);
    Route::get('/api/sales-by-store', [DatacenterController::class, 'salesByStore']);
    Route::get('/sales-by-store', [DatacenterController::class, 'showSalesByStore'])->name('sales.by.store');
    Route::get('/datacenter/payment-methods', [DatacenterController::class, 'paymentMethodsData'])->name('datacenter.paymentMethodsData');


    // Gestión de Productos
    Route::get('products/{id}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::post('products/{id}/switchStatus', [ProductController::class, 'switchStatus'])->name('products.switchStatus');

    // Gestión de Tiendas
    Route::prefix('stores/{store}')->name('stores.')->group(function () {
        Route::get('manage-users', [StoreController::class, 'manageUsers'])->name('manageUsers');
        Route::get('manage-hours', [StoreController::class, 'manageHours'])->name('manageHours');
        Route::post('associate-user', [StoreController::class, 'associateUser'])->name('associateUser');
        Route::post('disassociate-user', [StoreController::class, 'disassociateUser'])->name('disassociateUser');
        Route::post('save-hours', [StoreController::class, 'saveHours'])->name('saveHours');
        Route::post('toggle-store-status', [StoreController::class, 'toggleStoreStatus'])->name('toggle-status');
        Route::post('toggle-store-status-closed', [StoreController::class, 'toggleStoreStatusClosed'])->name('toggleStoreStatusClosed');
      });

    // Gestión de Roles
    Route::prefix('roles/{role}')->name('roles.')->group(function () {
        Route::get('manage-users', [RoleController::class, 'manageUsers'])->name('manageUsers');
        Route::post('associate-user', [RoleController::class, 'associateUser'])->name('associateUser');
        Route::post('disassociate-user', [RoleController::class, 'disassociateUser'])->name('disassociateUser');
        Route::get('manage-permissions', [RoleController::class, 'managePermissions'])->name('managePermissions');
        Route::post('assign-permissions', [RoleController::class, 'assignPermissions'])->name('assignPermissions');
    });

    // Gestión de Sabores de Productos
    Route::get('/product-flavors', [ProductController::class, 'flavors'])->name('product-flavors');
    Route::post('/product-flavors', [ProductController::class, 'storeFlavor'])->name('product-flavors.store-modal');
    Route::post('/product-flavors/multiple', [ProductController::class, 'storeMultipleFlavors'])->name('product-flavors.store-multiple');
    Route::delete('/product-flavors/{id}/delete', [ProductController::class, 'destroyFlavor'])->name('product-flavors.destroy');
    Route::put('/product-flavors/{id}/switch-status', [ProductController::class, 'switchFlavorStatus'])->name('flavors.switch-status');
    Route::get('/product-flavors/{id}', [ProductController::class, 'editFlavor'])->name('flavors.edit');
    Route::put('/product-flavors/{id}', [ProductController::class, 'updateFlavor'])->name('flavors.update');

    // CRM y Contabilidad
    Route::get('crm', [CrmController::class, 'index'])->name('crm');
    Route::get('receipts', [AccountingController::class, 'receipts'])->name('receipts');
    Route::get('entries', [AccountingController::class, 'entries'])->name('entries');
    Route::get('entrie', [AccountingController::class, 'entrie'])->name('entrie');

    // Ajustes de Comercio Electrónico
    Route::get('/ecommerce/marketing', [EcommerceController::class, 'marketing'])->name('marketing');
    Route::get('/ecommerce/settings', [EcommerceController::class, 'settings'])->name('settings');

    // Plantillas de Correos
    Route::get('/email-templates/edit/{templateId?}', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::post('/email-templates/update/{templateId?}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::post('/upload-image', [EmailTemplateController::class, 'uploadImage'])->name('upload-image');

    // Gestión de Ordenes
    Route::get('/orders/{order}/show', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{orderId}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}/pdf', [OrderPdfController::class, 'generatePdf'])->name('orders.pdf');

    // Gestión de Cupones
    Route::post('marketing/coupons/delete-selected', [CouponController::class, 'deleteSelected'])->name('coupons.deleteSelected');
    Route::get('coupons/{id}', [CouponController::class, 'show'])->name('coupons.show');

    // Gestión de categorías
    Route::delete('product-categories/{id}/delete-selected', [ProductCategoryController::class,'deleteSelected'])-> name('categories.deleteSelected');
    Route::post('product-categories/{id}/update-selected',[ProductCategoryController::class,'updateSelected'])-> name('categories.updateSelected');
    Route::get('product-categories/{id}/get-selected',[ProductCategoryController::class,'getSelected'])-> name('categories.getSelected');

    // Edición de Sabores
    Route::get('/flavors/{id}', [ProductController::class, 'editFlavor'])->name('flavors.edit');
    Route::put('/flavors/{id}', [ProductController::class, 'updateFlavor'])->name('flavors.update');

    // Órdenes a Proveedores
    Route::group(['prefix' => 'supplier-orders'], function () {
        Route::get('/{id}/pdf', [SupplierOrderController::class, 'generatePdf'])->name('supplier-orders.generatePdf');
    });

    // Omnicanalidad
    Route::group(['prefix' => 'omnichannel'], function () {
        // Configuración de WhatsApp
        Route::post('/update-meta-business-id', [OmnichannelController::class, 'updateMetaBusinessId'])->name('omnichannel.update.meta.business.id');
        Route::post('/update-admin-token', [OmnichannelController::class, 'updateMetaAdminToken'])->name('omnichannel.update.admin.token');

        // Asociar / Desasociar números de teléfono
        Route::post('/associate-phone', [OmnichannelController::class, 'associatePhoneNumberToStore'])->name('omnichannel.associate.phone');
        Route::post('/disassociate/{phone_id}', [OmnichannelController::class, 'disassociatePhoneNumberFromStore'])->name('omnichannel.disassociate');

        // Configuración
        Route::get('/settings', [OmnichannelController::class, 'settings'])->name('omnichannel.settings');

        // Chat
        Route::get('/', [OmnichannelController::class, 'chats'])->name('omnichannel.chat');
        Route::get('/fetch-messages', [WhatsAppController::class, 'fetchMessages'])->name('omnichannel.fetch.messages');
    });

    // Producciones
    Route::group(['prefix' => 'productions'], function () {
      Route::post('/activate/{production}', [ProductionController::class, 'activate'])->name('productions.activate');
      Route::post('/deactivate/{production}', [ProductionController::class, 'destroy'])->name('productions.deactivate');
    });
});

// Recursos con acceso público
Route::resources([
    'checkout' => CheckoutController::class,
]);


// E-Commerce
Route::get('/', [EcommerceController::class, 'home'])->name('home');
Route::get('shop', [EcommerceController::class, 'index'])->name('shop'); //
Route::get('store/{slug}', [EcommerceController::class, 'store'])->name('store'); // Tienda
Route::post('/cart/select-store', [CartController::class, 'selectStore'])->name('cart.selectStore'); // Seleccionar Tienda en el Carrito
Route::post('/cart/remove-item', [CartController::class, 'removeItem'])->name('cart.removeItem'); // Eliminar del Carrito
Route::get('/session/clear', [CartController::class, 'clearSession'])->name('session.clear'); // Limpiar Sesión
Route::get('/checkout/{orderId}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment'); // Pago de Orden
Route::get('/checkout/success/{order:uuid}', [CheckoutController::class, 'success'])->name('checkout.success'); // Pago Exitoso
Route::get('/checkout/pending/{order:uuid}', [CheckoutController::class, 'pending'])->name('checkout.pending'); // Pago Pendiente
Route::get('/checkout/failure/{order:uuid}', [CheckoutController::class, 'failure'])->name('checkout.failure'); // Pago Fallido
Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('apply.coupon'); // Aplicar Cupón

// Rutas de autenticación de Tienda Abierta
Route::middleware(['check.store.open'])->group(function () {
  Route::post('/cart/add/{productId}', [CartController::class, 'addToCart'])->name('cart.add');
  // Otras rutas que deben estar protegidas
});

// MercadoPago WebHooks
Route::post('/mpagohook', [MercadoPagoController::class, 'webhooks'])->name('mpagohook');

// Cambio de idioma
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// Test email
Route::get('/test-email', [EmailTemplateController::class, 'testEmail']);

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
