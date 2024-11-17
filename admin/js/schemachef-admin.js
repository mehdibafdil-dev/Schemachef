(function ($) {
    'use strict';

    $(document).ready(function () {
        var $submitDefaultBtn = $("#button_submit_recipe_default_data");
        var $submitGeneralBtn = $("#button_submit_recipe_general_settings_data");
        var $deleteRecipeBtns = $(".delete-recipe");

        $submitDefaultBtn.off('click').on('click', function (event) {
            var formData = $('#submit_recipe_default_data_form').serialize();
            var button = $(this);
            button.html('<i class="fas fa-spinner-third fa-spin"></i>');

            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'admin-post.php',
                data: {
                    action: 'save_recipe_options',
                    formData: formData,
                },
                success: handleSuccess(button),
                error: handleError(button),
            });
        });

        $submitGeneralBtn.off('click').on('click', function (event) {
            var formData = $('#submit_recipe_general_settings_form').serialize();
            var button = $(this);
            button.html('<i class="fas fa-spinner-third fa-spin"></i>');

            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'admin-post.php',
                data: {
                    action: 'save_recipe_general_settings',
                    formData: formData,
                },
                success: handleSuccess(button),
                error: handleError(button),
            });
        });

        $deleteRecipeBtns.on('click', function (event) {
            event.preventDefault();
            var recipeID = $(this).data("recipe-id");
            var confirmDelete = confirm("Are you sure you want to delete this recipe?");

            if (confirmDelete) {
                // Send AJAX request to delete the recipe
                $.ajax({
                    url: ajaxurl, // WordPress AJAX URL
                    type: "POST",
                    data: {
                        action: "delete_recipe_action",
                        recipe_id: recipeID,
                    },
                    success: function (response) {
                        if (response.success) {
                            iziToast.show({
                                title: 'Success',
                                message: 'Recipe deleted successfully',
                                color: 'green',
                                position: 'topRight',
                            });
                        } else {
                            // Error occurred while deleting the recipe
                            iziToast.show({
                                title: 'Error',
                                message: 'Error deleting recipe',
                                color: 'red',
                                position: 'topRight',
                            });
                        }
                    },
                    error: function () {
                        iziToast.show({
                            title: 'Error sending AJAX request',
                            message: '',
                            color: 'red',
                            position: 'topRight',
                        });
                    },
                });
            }
        });

    });

    function handleSuccess(button) {
        return function (response) {
            iziToast.show({
                title: 'Success',
                message: 'Your request was successful!',
                color: 'green',
                position: 'topRight',
            });
            button.html('Save Settings');
        };
    }

    function handleError(button) {
        return function (response) {
            iziToast.show({
                title: 'Error',
                message: response,
                color: 'red',
                position: 'topRight',
            });
            button.html('Save Settings');
        };
    }

})(jQuery);