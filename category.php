<?php
require_once '../settings/core.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../login/login.php");
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = getUserId();
$user_name = getUserName();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .header-section {
            background: linear-gradient(135deg, #D19C97, #b77a7a);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        
        .card-header {
            background-color: #D19C97;
            color: white;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: #D19C97;
            border-color: #D19C97;
        }
        
        .btn-primary:hover {
            background-color: #b77a7a;
            border-color: #b77a7a;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .loading {
            display: none;
        }
        
        .category-form {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-tags me-2"></i>Category Management</h2>
                    <p class="mb-0">Welcome, <?= htmlspecialchars($user_name ?? 'Admin') ?>! Manage your categories here.</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../index.php" class="btn btn-light">
                        <i class="fas fa-home me-1"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Add Category Form -->
        <div class="category-form">
            <h4><i class="fas fa-plus me-2"></i>Add New Category</h4>
            <form id="addCategoryForm">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" name="category_name" 
                                   placeholder="Enter category name" required maxlength="100">
                            <div class="form-text">Category name must be unique and cannot be empty.</div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" id="addCategoryBtn">
                            <span class="btn-text">
                                <i class="fas fa-plus me-1"></i>Add Category
                            </span>
                            <span class="loading">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Adding...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Categories List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Your Categories
                    <span class="badge bg-light text-dark ms-2" id="categoryCount">0</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="categoriesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading categories...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #D19C97; color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="editCategoryId" name="category_id">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="editCategoryName" name="category_name" 
                                   placeholder="Enter category name" required maxlength="100">
                            <div class="form-text">Category name must be unique and cannot be empty.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateCategoryBtn">
                        <span class="btn-text">
                            <i class="fas fa-save me-1"></i>Update Category
                        </span>
                        <span class="loading">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/category.js"></script>
</body>
</html>
