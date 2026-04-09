jQuery(document).ready(function ($) {
  var activeWrapper = null;

  function initModal() {
    if ($("#msdl-picker-modal").length === 0) {
      var modalHtml = `
                <div id="msdl-picker-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:999999; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
                    <div style="background:#fff; width:90%; max-width:850px; border-radius:8px; display:flex; flex-direction:column; max-height:85vh; overflow:hidden; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif; box-shadow: 0 15px 40px rgba(0,0,0,0.3);">
                        <div style="padding:15px 20px; background:#f0f6fc; border-bottom:1px solid #dcdcde; display:flex; justify-content:space-between; align-items:center;">
                            <h3 style="margin:0; font-size:18px; color:#1d2327; font-weight:600;"><span class="dashicons dashicons-portfolio" style="margin-top:2px;"></span> MSDL Dokumentumtár</h3>
                            <button class="msdl-close-modal" style="background:none; border:none; font-size:24px; cursor:pointer; color:#787c82; transition:color 0.2s;">&times;</button>
                        </div>
                        <div class="msdl-modal-body" style="padding:0; overflow-y:auto; flex:1; background:#fff;">
                            <div class="msdl-loader" style="padding:40px; text-align:center; color:#50575e; font-weight:500;">Adatok lekérése...</div>
                            <table class="msdl-items-table" style="width:100%; text-align:left; border-collapse:collapse; display:none; font-size:13px;">
                                <thead>
                                    <tr style="border-bottom:2px solid #dcdcde; color:#1d2327; background:#f6f7f7;">
                                        <th style="padding:12px 20px; font-weight:600;">Név</th>
                                        <th style="padding:12px 20px; width:90px; font-weight:600;">Méret</th>
                                        <th style="padding:12px 20px; width:180px; font-weight:600;">Láthatóság</th>
                                        <th style="padding:12px 20px; width:110px; font-weight:600;">Módosítva</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
      $("body").append(modalHtml);
      $("head").append(
        "<style>.msdl-item-row:hover{background-color:#f0f6fc !important;} .msdl-close-modal:hover{color:#d63638 !important;} .msdl-role-badge{padding:4px 10px; border-radius:12px; font-size:11px; font-weight:600; display:inline-block;}</style>",
      );
    }
  }

  $("body").on("click", ".msdl-open-picker-btn", function (e) {
    e.preventDefault();
    initModal();
    activeWrapper = $(this).closest(".msdl-picker-control-wrapper");
    var type = $(this).data("item-type");

    $("#msdl-picker-modal").css("display", "flex");
    $(".msdl-items-table").hide();
    $(".msdl-loader").html("Adatok lekérése a szerverről...").show();

    $.post(
      msdlPickerData.ajax_url,
      {
        action: "msdl_get_picker_items",
        nonce: msdlPickerData.nonce,
        item_type: type,
      },
      function (response) {
        $(".msdl-loader").hide();
        var tbody = $(".msdl-items-table tbody");
        tbody.empty();

        if (response.success && response.data && response.data.length > 0) {
          response.data.forEach(function (item) {
            // Ikon logika (Emoji helyett Dashicons)
            var iconHtml =
              type === "folder"
                ? '<span class="dashicons dashicons-category" style="color:#f5c342; margin-right:8px; margin-top:2px;"></span>'
                : '<span class="dashicons dashicons-media-document" style="color:#2271b1; margin-right:8px; margin-top:2px;"></span>';

            // Dinamikus címkeszín a jogosultság alapján
            var roleBg = "#e2e4e7";
            var roleColor = "#2c3338";
            if (item.roles.includes("Mindenki")) {
              roleBg = "#d1e7dd";
              roleColor = "#0f5132";
            } else if (item.roles.includes("Bejelentkezett")) {
              roleBg = "#cfe2ff";
              roleColor = "#084298";
            } else {
              roleBg = "#fff3cd";
              roleColor = "#664d03";
            } // Adminisztrátor vagy specifikus

            var badgeHtml = `<span class="msdl-role-badge" style="background:${roleBg}; color:${roleColor};">${item.roles}</span>`;

            tbody.append(`
                        <tr class="msdl-item-row" data-id="${item.id}" data-name="${item.name}" data-size="${item.size}" data-roles="${item.roles}" data-date="${item.date}" style="cursor:pointer; border-bottom:1px solid #f0f0f0;">
                            <td style="padding:12px 20px; color:#1d2327; display:flex; align-items:center;"><strong style="font-size:14px; color:#2271b1;">${iconHtml} ${item.name}</strong></td>
                            <td style="padding:12px 20px; color:#50575e;">${item.size}</td>
                            <td style="padding:12px 20px;">${badgeHtml}</td>
                            <td style="padding:12px 20px; color:#50575e;">${item.date}</td>
                        </tr>
                    `);
          });
          $(".msdl-items-table").show();
        } else {
          tbody.append(
            '<tr><td colspan="4" style="padding:30px; text-align:center; color:#50575e;">Nincs találat az adatbázisban.</td></tr>',
          );
          $(".msdl-items-table").show();
        }
      },
    ).fail(function (xhr, status, error) {
      $(".msdl-loader").html(
        '<span style="color:#d63638;">Hiba: ' + error + "</span>",
      );
    });
  });

  $("body").on("click", ".msdl-item-row", function () {
    var item = $(this).data();
    if (activeWrapper) {
      activeWrapper
        .find(".msdl-picker-hidden-input")
        .val(item.id)
        .trigger("input");
      var card = activeWrapper.find(".msdl-selected-card");
      card.find(".msdl-sc-name").text(item.name);
      card.find(".msdl-sc-size").text(item.size);
      card.find(".msdl-sc-roles").text(item.roles);
      card.find(".msdl-sc-date").text(item.date);
      card.slideDown("fast");
    }
    $("#msdl-picker-modal").hide();
  });

  $("body").on("click", ".msdl-clear-btn", function (e) {
    e.preventDefault();
    var wrapper = $(this).closest(".msdl-picker-control-wrapper");
    wrapper.find(".msdl-picker-hidden-input").val("").trigger("input");
    wrapper.find(".msdl-selected-card").slideUp("fast");
  });

  $("body").on("click", ".msdl-close-modal", function () {
    $("#msdl-picker-modal").hide();
  });

  $("body").on("mouseenter", ".msdl-picker-control-wrapper", function () {
    var wrapper = $(this);
    var input = wrapper.find(".msdl-picker-hidden-input");
    var val = input.val();
    var card = wrapper.find(".msdl-selected-card");

    if (val && !card.hasClass("msdl-loaded")) {
      card.addClass("msdl-loaded");
      $.post(
        msdlPickerData.ajax_url,
        {
          action: "msdl_get_single_item",
          nonce: msdlPickerData.nonce,
          item_id: val,
        },
        function (response) {
          if (response.success) {
            card.find(".msdl-sc-name").text(response.data.name);
            card.find(".msdl-sc-size").text(response.data.size);
            card.find(".msdl-sc-roles").text(response.data.roles);
            card.find(".msdl-sc-date").text(response.data.date);
            card.show();
          }
        },
      );
    }
  });
});
