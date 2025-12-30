<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ---- Controllers (Public / User) ----
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\Web\PharmacyController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\GuardPublicController;
use App\Http\Controllers\ProductsOrdersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// ---- Controllers (Admin) ----
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPharmacyController;
use App\Http\Controllers\Admin\AdminGuardController;
use App\Http\Controllers\Admin\AdminGuardPharmacyController;
use App\Http\Controllers\Admin\AdminPharmacistController;
use App\Http\Controllers\Admin\PharmacistTokenController;


// ---- Controllers (Pharmacist) ----
use App\Http\Controllers\Pharmacist\PharmacistDashboardController;
use App\Http\Controllers\Pharmacist\PharmacistAuthController;
use App\Http\Controllers\PharmacistApiController;
use App\Http\Controllers\Pharmacist\PharmacistOrderController;
use App\Http\Controllers\Pharmacist\PharmacistReportController;
use App\Http\Controllers\PharmacistManageController;
use App\Http\Controllers\Pharmacist\PharmacistGeoController;
use App\Http\Controllers\Pharmacist\StockController;
use App\Http\Controllers\Pharmacist\SupplierController;
/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/cors-test', 'cors-test');

// Pharmacies (public)
Route::get('/pharmacies', [PharmacyController::class, 'index'])->name('pharmacies.index');

// Recherche produits (public)
Route::get('/recherche-produits', [ProductController::class, 'search'])->name('products.search');
Route::post('/recherche-produits', [ProductController::class, 'submitForfait'])->name('products.plan.submit');

// Catalogue public
Route::get('/catalogue', [CatalogueController::class, 'index']);

// Contact
Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Confirmation création de compte
Route::view('/account-created', 'auth.account-created')->name('account.created');

/*
|--------------------------------------------------------------------------
| USER AUTHENTIFIED (NON-ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'not_admin'])->group(function () {

    // Dashboard utilisateur
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Page Produits & Commande
    Route::get('/produits-commande', [ProductsOrdersController::class, 'index'])
        ->name('produits.commande');
    
    // Nouvelle page : choisir une pharmacie partenaire
Route::get('/produits-commande/pharmacies', [ProductsOrdersController::class, 'selectPharmacy'])
    ->name('produits.commande.pharmacies');

// Ordonnance (téléversement + recherche de pharmacie via ordonnance)
Route::post('/produits-commande/ordonnance/upload', [ProductsOrdersController::class, 'uploadPrescription'])
    ->name('produits.commande.ordonnance.upload');

Route::post('/produits-commande/ordonnance/search', [ProductsOrdersController::class, 'searchPharmacyByPrescription'])
    ->name('produits.commande.ordonnance.search');

Route::post('/produits-commande/ordonnance/clear', [ProductsOrdersController::class, 'clearPrescription'])
    ->name('produits.commande.ordonnance.clear');

// Nouvelle page : produits d'une pharmacie choisie
Route::get('/produits-commande/pharmacies/{pharmacy}', [ProductsOrdersController::class, 'pharmacyProducts'])
    ->name('produits.commande.pharmacy');


    // Commande (création + consultation)
    Route::post('/commande/envoyer', [OrderController::class, 'store'])->name('order.store');
    Route::get('/commande/voir',     [OrderController::class, 'current'])->name('order.current');
    Route::get('/commande/{orderId}',[OrderController::class, 'show'])->name('order.show');

    // Liste des commandes (Voir mes commandes)
    Route::get('/commandes', [OrderController::class, 'index'])->name('orders.index');

    // Actions sur une commande
    Route::post('/commande/{orderId}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::post('/commande/{orderId}/delete', [OrderController::class, 'destroy'])->name('order.destroy');

    // Panier (session)
    Route::get('/cart',        [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',   [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove',[CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});

/*
|--------------------------------------------------------------------------
| USER PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',        [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',      [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',     [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth admin
    Route::get('/login',  [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    // 2FA (auth requis mais pas encore admin.2fa)
    Route::middleware(['auth'])->group(function () {

        Route::get('/verify',  [AdminAuthController::class, 'showVerifyForm'])->name('verify.form');
        Route::post('/verify', [AdminAuthController::class, 'verify'])->name('verify.submit');
        Route::post('/resend', [AdminAuthController::class, 'resendCode'])->name('verify.resend');

        // Zone admin protégée
        Route::middleware(['is_admin','admin.2fa'])->group(function () {

            // Dashboard + logout
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::post('/logout',   [AdminAuthController::class, 'logout'])->name('logout');

            // Profil admin
            Route::middleware('auth')->group(function () {
                Route::get('/profile',               [ProfileController::class, 'edit'])->name('profile.edit');
                Route::patch('/profile',             [ProfileController::class, 'update'])->name('profile.update');
                Route::put('/profile/password',      [ProfileController::class, 'updatePassword'])->name('profile.password.update');
            });

            // Utilisateurs
            Route::get('/users',           [AdminUserController::class, 'index'])->name('users.index');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
            Route::get('/users/export',    [AdminUserController::class, 'export'])->name('users.export');

            // Pharmaciens
            Route::get('/pharmacists',            [AdminPharmacistController::class, 'index'])->name('pharmacists.index');
            Route::get('/pharmacists/create',     [AdminPharmacistController::class, 'create'])->name('pharmacists.create');
            Route::post('/pharmacists',           [AdminPharmacistController::class, 'store'])->name('pharmacists.store');

            // ✏️ Modifier un pharmacien
            Route::get('/pharmacists/{pharmacist}/edit', [AdminPharmacistController::class, 'edit'])
                ->name('pharmacists.edit');

            // ✅ Mise à jour 
            Route::put('/pharmacists/{pharmacist}', [AdminPharmacistController::class, 'update'])
                ->name('pharmacists.update');

            // 🔐 Page Token + sauvegarde
            Route::get('/pharmacists/{pharmacist}/token', [PharmacistTokenController::class, 'edit'])
                ->name('pharmacists.token');

            Route::post('/pharmacists/{pharmacist}/token', [PharmacistTokenController::class, 'update'])
                ->name('pharmacists.token.update');

            // 🗑 Suppression
            Route::delete('/pharmacists/{pharmacist}',  [AdminPharmacistController::class, 'destroy'])
                ->name('pharmacists.destroy');


            // Pharmacies
            Route::get('/pharmacies',                       [AdminPharmacyController::class, 'index'])->name('pharmacies.index');
            Route::get('/pharmacies/create',                [AdminPharmacyController::class, 'create'])->name('pharmacies.create');
            Route::post('/pharmacies',                      [AdminPharmacyController::class, 'store'])->name('pharmacies.store');
            Route::get('/pharmacies/{pharmacy}/edit',       [AdminPharmacyController::class, 'edit'])->name('pharmacies.edit');
            Route::put('/pharmacies/{pharmacy}',            [AdminPharmacyController::class, 'update'])->name('pharmacies.update');
            Route::delete('/pharmacies/{pharmacy}',         [AdminPharmacyController::class, 'destroy'])->name('pharmacies.destroy');
            Route::get('/pharmacies/export',                [AdminPharmacyController::class, 'export'])->name('pharmacies.export');

            // Pharmacies de garde (ROTATION)
            Route::get('/guards', [AdminGuardController::class, 'index'])->name('guards.index');

            // Config rotation (date/heure + groupe départ)
            Route::get('/guards/create', [AdminGuardController::class, 'create'])->name('guards.create');
            Route::post('/guards', [AdminGuardController::class, 'store'])->name('guards.store');

            // Voir un groupe (1..5)
            Route::get('/guards/{group}', [AdminGuardController::class, 'show'])->name('guards.show');

            // Import Excel/CSV d’un groupe
            Route::post('/guards/{group}/import', [AdminGuardController::class, 'import'])->name('guards.import');


            // Import “tout type”
            Route::post('/guards/{schedule}/import',  [AdminGuardController::class, 'import'])->name('guards.import');

            // CRUD pharmacies de garde
            Route::post('/guards/{schedule}/pharmacies',                [AdminGuardPharmacyController::class, 'store'])->name('guards.pharmacies.store');
            Route::get('/guards/{schedule}/pharmacies/{pharmacy}/edit', [AdminGuardPharmacyController::class, 'edit'])->name('guards.pharmacies.edit');
            Route::put('/guards/{schedule}/pharmacies/{pharmacy}',      [AdminGuardPharmacyController::class, 'update'])->name('guards.pharmacies.update');
            Route::delete('/guards/{schedule}/pharmacies/{pharmacy}',   [AdminGuardPharmacyController::class, 'destroy'])->name('guards.pharmacies.destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| PHARMACIEN
|--------------------------------------------------------------------------
|
| TOUT ce qui commence par /pharmacist/... arrive ici.
| Donc PAS besoin de réécrire /pharmacist/... à l’intérieur.
|
*/
Route::prefix('pharmacist')
    ->name('pharmacist.')
    ->middleware(['auth', 'is_pharmacist'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Sécurité / 1ère connexion + 2FA
        |--------------------------------------------------------------------------
        */

        // Changement de mot de passe à la première connexion
        Route::get('/password/change', [PharmacistAuthController::class, 'showChangePasswordForm'])
            ->name('password.form');
        Route::post('/password/change', [PharmacistAuthController::class, 'changePassword'])
            ->name('password.change');

        // 2FA (code envoyé par email)
        Route::get('/verify',  [PharmacistAuthController::class, 'showVerifyForm'])
            ->name('verify.form');
        Route::post('/verify', [PharmacistAuthController::class, 'verify'])
            ->name('verify.submit');
        Route::post('/resend', [PharmacistAuthController::class, 'resendCode'])
            ->name('verify.resend');

        /*
        |--------------------------------------------------------------------------
        | Étape GEO (après 2FA)
        |--------------------------------------------------------------------------
        */
        Route::middleware(['pharmacist.2fa'])->group(function () {

            // Page de géolocalisation
            Route::get('/geo', [PharmacistGeoController::class, 'page'])
                ->name('geo.page');

            // Endpoint AJAX de vérification
            Route::post('/geo/check', [PharmacistGeoController::class, 'check'])
                ->name('geo.check');
        });

        /*
        |--------------------------------------------------------------------------
        | Zone principale Pharmacien
        | Protégée par : auth + is_pharmacist + pharmacist.2fa + pharmacy.geo
        |--------------------------------------------------------------------------
        */
        Route::middleware(['pharmacist.2fa', 'pharmacy.geo'])->group(function () {

            // Tableau de bord
            Route::get('/dashboard', [PharmacistDashboardController::class, 'index'])
                ->name('dashboard');

            // Page de gestion (Centre de gestion)
            Route::get('/manage', [PharmacistManageController::class, 'index'])
                ->name('manage');

            /*
            |----------------------------------------------------------------------
            | Commandes
            |----------------------------------------------------------------------
            */
            Route::get('/orders', [PharmacistOrderController::class, 'index'])
                ->name('orders.index');

            Route::get('/orders/{order}', [PharmacistOrderController::class, 'show'])
                ->name('orders.show');

            Route::post('/orders/{order}/status', [PharmacistOrderController::class, 'updateStatus'])
                ->name('orders.updateStatus');

            // Nombre de commandes en attente (JSON) pour le dashboard + alerte sonore
            Route::get('/orders/pending-count', [PharmacistOrderController::class, 'pendingCount'])
                ->name('orders.pendingCount');

        

            /*
            |----------------------------------------------------------------------
            | Rapports
            |----------------------------------------------------------------------
            */
            Route::get('/reports', [PharmacistReportController::class, 'index'])
                ->name('reports');

            /*
            |----------------------------------------------------------------------
            | GESTION API PHARMACIEN (AbdouPharma)
            | URL :
            |   GET  /pharmacist/api
            |   POST /pharmacist/api/test
            |   POST /pharmacist/api/load
            |   GET  /pharmacist/api/products
            |----------------------------------------------------------------------
            */
            Route::get('/api', [PharmacistApiController::class, 'show'])
                ->name('api');

            Route::post('/api/test', [PharmacistApiController::class, 'test'])
                ->name('api.test');

            Route::post('/api/load', [PharmacistApiController::class, 'load'])
                ->name('api.load');

            Route::get('/api/products', [PharmacistApiController::class, 'products'])
                ->name('api.products');

            /*
            |----------------------------------------------------------------------
            | GESTION DE STOCK
            |----------------------------------------------------------------------
            */
            // Page principale du stock
            Route::get('/stock', [StockController::class, 'index'])
                ->name('stock.index');

            // Enregistrer une entrée de stock (réception fournisseur)
            Route::post('/stock/entry', [StockController::class, 'storeEntry'])
                ->name('stock.entry.store');

            // Historique des mouvements pour un produit
            Route::get('/stock/{product}/movements', [StockController::class, 'movements'])
                ->name('stock.movements');

            // Liste des alertes de stock (produits en stock bas / rupture)
            Route::get('/stock/alerts', [StockController::class, 'alerts'])
                ->name('stock.alerts');

            // Contact fournisseur depuis une alerte de stock
            Route::post('/stock/contact-supplier', [StockController::class, 'contactSupplier'])
                ->name('stock.contact');

            // Export CSV/Excel du stock
            Route::get('/stock/export', [StockController::class, 'export'])
                ->name('stock.export');
            
            Route::post('/stock/contact', [StockController::class, 'contactSupplier'])->name('stock.contact');


            /*
            |----------------------------------------------------------------------
            | FOURNISSEURS (CRUD complet)
            | URL :
            |   GET    /pharmacist/suppliers          (index)
            |   POST   /pharmacist/suppliers          (store)
            |   GET    /pharmacist/suppliers/{id}/edit (edit)
            |   PUT    /pharmacist/suppliers/{id}     (update)
            |   DELETE /pharmacist/suppliers/{id}     (destroy)
            |----------------------------------------------------------------------
            */
            Route::get('/suppliers', [SupplierController::class, 'index'])
                ->name('suppliers.index');

            Route::post('/suppliers', [SupplierController::class, 'store'])
                ->name('suppliers.store');

            Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])
                ->name('suppliers.edit');

            Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])
                ->name('suppliers.update');

            Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])
                ->name('suppliers.destroy');
        });
    });

/*
|--------------------------------------------------------------------------
| PHARMACIES DE GARDE (public)
|--------------------------------------------------------------------------
*/
Route::get('/pharmacies-de-garde', [GuardPublicController::class, 'index'])->name('guards.public');
Route::get('/pharmacies-de-garde/p/{offset}', [GuardPublicController::class, 'page'])
    ->whereNumber('offset')
    ->name('guards.public.page');
