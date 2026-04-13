<?php

use Illuminate\Support\Facades\Route;

// Space web routes are handled by the Admin and Vendor modules.
// - Admin: Modules/Admin/routes/web.php (admin.spaces.*)
// - Vendor: Modules/Vendor/routes/web.php (vendor.spaces.*)
// - API: Modules/Space/routes/api.php (spaces/*)
//
// This file is intentionally left minimal — the Space module provides
// models, actions, DTOs, and API controllers, while CRUD is done
// through the Admin/Vendor panel controllers.
