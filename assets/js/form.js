$(function () {
  $("#username").keyup(function () {
    $(this).val(
      $(this)
        .val()
        .toLowerCase()
        .replace(/[^a-z0-9]/g, "")
    );
  });

  let lastUsername = "";
  $("#username").focusout(function () {
    const username = encodeURIComponent($(this).val().trim());
    if (username.length >= 3 && username != lastUsername) {
      $.ajax({
        url: `ajax/cek_available_username.php?username=${username}`,
        success: function (r) {
          lastUsername = username;
          if (r.trim() == "OK") {
            $("#username--available").html(
              `<span class='text-success'>Username ${username} tersedia.</span>`
            );
          } else {
            $("#username--available").html(r);
          }
        },
        error: function () {
          $("#username--available").html(
            "<span class='text-danger'>Gagal cek username, coba lagi nanti.</span>"
          );
        },
      });
    } else {
      $("#username--available").html(`Username minimal 3 karakter.`);
    }
  });

  $("#nama").keyup(function () {
    $(this).val(
      $(this)
        .val()
        .replace(/['"]/g, "`")
        .replace(/[^a-zA-Z\s]/g, "")
        .replace(/  /g, " ")
        .replace(/\w\S*/g, function (txt) {
          return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        })
    );
  });

  $("#whatsapp").keyup(function () {
    let val = $(this)
      .val()
      .replace(/[^0-9]/g, ""); // Hanya angka
    if (val.startsWith("08")) {
      val = "628" + val.substring(2);
    } else if (!val.startsWith("628") && val.length >= 4) {
      val = "";
    }
    $(this).val(val);
  });

  $("input,textarea,select").focus(function () {
    let id = $(this).prop("id");
    $(`#${id}--info`).slideDown();
  });
  $("input,textarea,select").focusout(function () {
    let id = $(this).prop("id");
    $(`#${id}--info`).slideUp();
  });
});
