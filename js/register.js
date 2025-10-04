$(document).ready(function () {
  $("#register-form").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this); // ✅ Correct way for multipart/form-data

    $.ajax({
      url: "register_user_action.php", // adjust if in a different directory
      type: "POST",
      data: formData,
      processData: false,  // ✅ important
      contentType: false,  // ✅ important
      beforeSend: function () {
        $("#register-btn .btn-text").hide();
        $("#register-btn .loading").show();
      },
      success: function (response) {
        try {
          let res = JSON.parse(response);
          if (res.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Registration Successful!",
              text: res.message,
              confirmButtonColor: "#D19C97"
            }).then(() => {
              window.location.href = "login.php";
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: res.message,
              confirmButtonColor: "#D19C97"
            });
          }
        } catch (e) {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Unexpected error. Please try again.",
            confirmButtonColor: "#D19C97"
          });
        }
      },
      complete: function () {
        $("#register-btn .btn-text").show();
        $("#register-btn .loading").hide();
      }
    });
  });
});
