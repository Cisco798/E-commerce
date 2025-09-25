$(document).ready(function() {
    // Load categories on page load
    loadCategories();

    // Add Category Form Submit
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault();
        addCategory();
    });

    // Edit Category Form Submit
    $('#updateCategoryBtn').click(function(e) {
        e.preventDefault();
        updateCategory();
    });

    // Handle Edit button clicks (delegated event)
    $(document).on('click', '.edit-category-btn', function() {
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');
        showEditModal(categoryId, categoryName);
    });

    // Handle Delete button clicks (delegated event)
    $(document).on('click', '.delete-category-btn', function() {
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');
        confirmDelete(categoryId, categoryName);
    });
});

/**
 * Load and display categories
 */
function loadCategories() {
    $.ajax({
        url: '../actions/fetch_category_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                displayCategories(response.categories);
                updateCategoryCount(response.count);
            } else {
                displayError('Failed to load categories: ' + response.message);
                displayEmptyState();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            displayError('An error occurred while loading categories');
            displayEmptyState();
        }
    });
}

/**
 * Display categories in the table
 */
function displayCategories(categories) {
    const tbody = $('#categoriesTableBody');
    tbody.empty();

    if (categories.length === 0) {
        displayEmptyState();
        return;
    }

    categories.forEach(function(category, index) {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${category.category_name}</strong></td>
                <td>${category.formatted_date}</td>
                <td class="action-buttons">
                    <button class="btn btn-sm btn-outline-primary edit-category-btn" 
                            data-id="${category.category_id}" 
                            data-name="${category.category_name}"
                            title="Edit Category">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-category-btn" 
                            data-id="${category.category_id}" 
                            data-name="${category.category_name}"
                            title="Delete Category">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

/**
 * Display empty state when no categories
 */
function displayEmptyState() {
    const tbody = $('#categoriesTableBody');
    tbody.html(`
        <tr>
            <td colspan="4" class="text-center py-4">
                <div class="text-muted">
                    <i class="fas fa-tags fa-2x mb-2"></i>
                    <p>No categories found. Create your first category above!</p>
                </div>
            </td>
        </tr>
    `);
    updateCategoryCount(0);
}

/**
 * Update category count badge
 */
function updateCategoryCount(count) {
    $('#categoryCount').text(count);
}

/**
 * Add a new category
 */
function addCategory() {
    const categoryName = $('#categoryName').val().trim();
    const addBtn = $('#addCategoryBtn');

    // Validate category name
    if (!validateCategoryName(categoryName)) {
        return;
    }

    // Show loading state
    showButtonLoading(addBtn);

    $.ajax({
        url: '../actions/add_category_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            category_name: categoryName
        },
        success: function(response) {
            hideButtonLoading(addBtn);

            if (response.status === 'success') {
                showSuccess(response.message);
                $('#addCategoryForm')[0].reset();
                loadCategories(); // Reload categories
            } else {
                showError(response.message);
            }
        },
        error: function(xhr, status, error) {
            hideButtonLoading(addBtn);
            console.error('Add Category AJAX Error:', error);
            showError('An error occurred while adding the category');
        }
    });
}

/**
 * Show edit modal with category data
 */
function showEditModal(categoryId, categoryName) {
    $('#editCategoryId').val(categoryId);
    $('#editCategoryName').val(categoryName);
    $('#editCategoryModal').modal('show');
}

/**
 * Update category
 */
function updateCategory() {
    const categoryId = $('#editCategoryId').val();
    const categoryName = $('#editCategoryName').val().trim();
    const updateBtn = $('#updateCategoryBtn');

    // Validate category name
    if (!validateCategoryName(categoryName)) {
        return;
    }

    // Show loading state
    showButtonLoading(updateBtn);

    $.ajax({
        url: '../actions/update_category_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            category_id: categoryId,
            category_name: categoryName
        },
        success: function(response) {
            hideButtonLoading(updateBtn);

            if (response.status === 'success') {
                showSuccess(response.message);
                $('#editCategoryModal').modal('hide');
                loadCategories(); // Reload categories
            } else {
                showError(response.message);
            }
        },
        error: function(xhr, status, error) {
            hideButtonLoading(updateBtn);
            console.error('Update Category AJAX Error:', error);
            showError('An error occurred while updating the category');
        }
    });
}

/**
 * Confirm category deletion
 */
function confirmDelete(categoryId, categoryName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the category "${categoryName}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i>Yes, delete it!',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCategory(categoryId, categoryName);
        }
    });
}

/**
 * Delete category
 */
function deleteCategory(categoryId, categoryName) {
    // Show loading indicator
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the category.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '../actions/delete_category_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            category_id: categoryId
        },
        success: function(response) {
            if (response.status === 'success') {
                showSuccess(`Category "${categoryName}" deleted successfully`);
                loadCategories(); // Reload categories
            } else {
                showError(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete Category AJAX Error:', error);
            showError('An error occurred while deleting the category');
        }
    });
}

/**
 * Validate category name
 */
function validateCategoryName(categoryName) {
    if (categoryName === '') {
        showError('Category name is required');
        return false;
    }

    if (categoryName.length < 2) {
        showError('Category name must be at least 2 characters long');
        return false;
    }

    if (categoryName.length > 100) {
        showError('Category name cannot exceed 100 characters');
        return false;
    }

    // Check for special characters (allow letters, numbers, spaces, hyphens, underscores)
    const validNameRegex = /^[a-zA-Z0-9\s\-_]+$/;
    if (!validNameRegex.test(categoryName)) {
        showError('Category name can only contain letters, numbers, spaces, hyphens, and underscores');
        return false;
    }

    return true;
}

/**
 * Show button loading state
 */
function showButtonLoading(button) {
    button.prop('disabled', true);
    button.find('.btn-text').hide();
    button.find('.loading').show();
}

/**
 * Hide button loading state
 */
function hideButtonLoading(button) {
    button.prop('disabled', false);
    button.find('.loading').hide();
    button.find('.btn-text').show();
}

/**
 * Show success message
 */
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}

/**
 * Show error message
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: message
    });
}

/**
 * Display general error message
 */
function displayError(message) {
    console.error(message);
    // You could also show a toast notification here if desired
}
