jQuery(document).ready(function ($) {
  var activeWrapper = null;
  var currentItemType = "file";
  var breadcrumbs = [{ id: "root", name: "Dokumentumtár (Gyökér)" }];
  var currentFolderId = "root";
  var currentSearch = "";

  function renderBreadcrumbs() {
    var html = "";
    breadcrumbs.forEach(function (b, i) {
      if (i === breadcrumbs.length - 1) {
        html += `<span style="color:#1d2327; font-weight:600; background:#fff; padding:4px 10px; border-radius:4px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">${b.name}</span>`;
      } else {
        html += `<a href="#" class="msdl-bc-link" data-index="${i}" style="color:#2271b1; text-decoration:none; font-weight:500; padding:4px 8px; border-radius:4px; transition:background 0.2s;" onmouseover="this.style.background='#e7f0f7'" onmouseout="this.style.background='transparent'">${b.name}</a> <span style="margin:0 6px; color:#a0a6b5;">/</span> `;
      }
    });
    return html;
  }

  function initModal() {
    if ($("#msdl-picker-modal").length === 0) {
      var modalHtml = `
        <div id="msdl-picker-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:999999; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
            <div style="background:#fff; width:95%; max-width:1000px; border-radius:8px; display:flex; flex-direction:column; max-height:85vh; overflow:hidden; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif; box-shadow: 0 15px 40px rgba(0,0,0,0.3);">
                
                <div style="padding:15px 20px; background:#f0f6fc; border-bottom:1px solid #dcdcde; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0; font-size:18px; color:#1d2327; font-weight:600; display:flex; align-items:center; gap:8px;">
                        <span class="dashicons dashicons-portfolio" style="color:#2271b1; margin-top:2px;"></span> MSDL Tallózó
                    </h3>
                    <button class="msdl-close-modal" style="background:none; border:none; font-size:26px; cursor:pointer; color:#8c8f94; transition:color 0.2s; line-height:1;" onmouseover="this.style.color='#d63638'" onmouseout="this.style.color='#8c8f94'">&times;</button>
                </div>

                <div style="padding:15px 20px; background:#f6f7f7; border-bottom:1px solid #e2e4e7; display:flex; justify-content:space-between; align-items:center; gap:15px; flex-wrap:wrap;">
                    <div class="msdl-breadcrumbs" style="font-size:14px; flex-grow:1; display:flex; align-items:center;"></div>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="text" id="msdl-picker-search" placeholder="Keresés név vagy cím alapján..." style="padding:8px 12px; border:1px solid #8c8f94 !important; border-radius:4px; font-size:13px; width:260px; background-color:#ffffff !important; color:#1d2327 !important; box-shadow:inset 0 1px 2px rgba(0,0,0,0.05);">
                        <button id="msdl-select-current-folder" style="display:none; background:#2271b1; color:#fff; border:none; padding:8px 15px; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#135e96'" onmouseout="this.style.background='#2271b1'">Jelenlegi mappa kiválasztása</button>
                    </div>
                </div>

                <div class="msdl-modal-body" style="padding:0; overflow-y:auto; flex:1; background:#fff; position:relative;">
                    <div class="msdl-loader" style="padding:40px; text-align:center; color:#50575e; font-weight:500;">Adatok lekérése...</div>
                    <table class="msdl-items-table" style="width:100%; text-align:left; border-collapse:collapse; display:none; font-size:13px; table-layout:fixed;">
                        <thead>
                            <tr style="border-bottom:2px solid #dcdcde; color:#1d2327; background:#fff;">
                                <th style="padding:12px 20px; font-weight:600;">Név / Cím</th>
                                <th style="padding:12px 10px; width:65px; font-weight:600; text-align:center;">Leírás</th>
                                <th style="padding:12px 10px; width:100px; font-weight:600; text-align:center;">Láthatóság</th>
                                <th style="padding:12px 10px; width:80px; font-weight:600; text-align:right;">Méret</th>
                                <th style="padding:12px 10px; width:100px; font-weight:600;">Módosítva</th>
                                <th style="padding:12px 20px; width:180px; font-weight:600; text-align:right;">Művelet</th>
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
        "<style>.msdl-row-hover:hover{background-color:#f0f6fc !important;} .msdl-action-btn{padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid transparent; transition:all 0.2s; white-space:nowrap;} .msdl-btn-open{background:#fff; color:#2271b1; border-color:#2271b1;} .msdl-btn-open:hover{background:#f0f6fc;} .msdl-btn-select{background:#2271b1; color:#fff; border-color:#2271b1;} .msdl-btn-select:hover{background:#135e96;}</style>",
      );
    }
  }

  function loadItems() {
    $(".msdl-items-table").hide();
    $(".msdl-loader").show();
    $(".msdl-breadcrumbs").html(renderBreadcrumbs());

    if (currentItemType === "folder" && currentSearch === "") {
      $("#msdl-select-current-folder").show();
    } else {
      $("#msdl-select-current-folder").hide();
    }

    $.post(
      msdlPickerData.ajax_url,
      {
        action: "msdl_get_picker_items",
        nonce: msdlPickerData.nonce,
        parent_id: currentFolderId,
        search: currentSearch,
      },
      function (response) {
        $(".msdl-loader").hide();
        var tbody = $(".msdl-items-table tbody");
        tbody.empty();

        if (response.success && response.data && response.data.length > 0) {
          var validItems = response.data.filter(function (item) {
            return item.roles !== "hidden";
          });

          if (validItems.length > 0) {
            validItems.forEach(function (item) {
              var isFolder = item.type === "folder";
              if (currentItemType === "folder" && !isFolder) return;

              var iconHtml = isFolder
                ? '<span class="dashicons dashicons-portfolio" style="color:#f5c342; margin-right:12px; font-size:24px; width:24px; height:24px; flex-shrink:0;"></span>'
                : '<span class="dashicons dashicons-media-document" style="color:#72aee6; margin-right:12px; font-size:24px; width:24px; height:24px; flex-shrink:0;"></span>';

              var titleHtml = "";
              if (item.custom_title && item.custom_title.trim() !== "") {
                titleHtml = `<div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><strong style="font-size:14px; color:#007cba;">${item.custom_title}</strong><div style="font-size:11px; color:#8c8f94; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${item.name}</div></div>`;
              } else {
                titleHtml = `<div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><strong style="font-size:14px; color:${
                  isFolder ? "#2271b1" : "#1d2327"
                };">${item.name}</strong></div>`;
              }

              var hasDesc =
                item.custom_description &&
                item.custom_description.trim() !== "";
              var descHtml = hasDesc
                ? '<span class="dashicons dashicons-yes-alt" style="color:#00a32a;" title="A fájlhoz tartozik HTML leírás"></span>'
                : '<span style="color:#ccd0d4;" title="Nincs leírás">-</span>';

              var roleStr = item.visibility_roles || item.roles || "public";
              var accessIcon = "dashicons-admin-site-alt3";
              var accessTooltip = "Nyilvános (Bárki láthatja)";

              if (roleStr === "loggedin") {
                accessIcon = "dashicons-lock";
                accessTooltip = "Csak bejelentkezett felhasználók";
              } else if (roleStr.includes("[")) {
                accessIcon = "dashicons-admin-users";
                // JSON tömb dekódolása és emberi olvashatóvá tétele
                try {
                  var parsedRoles = JSON.parse(roleStr);
                  var formattedRoles = parsedRoles
                    .map(function (r) {
                      return r.charAt(0).toUpperCase() + r.slice(1);
                    })
                    .join(", ");
                  accessTooltip = "Engedélyezve: " + formattedRoles;
                } catch (e) {
                  accessTooltip = "Szerepkörhöz kötött";
                }
              }

              var badgeHtml = `<div title="${accessTooltip}" style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; background:#f0f6fc; border-radius:50%; color:#2271b1; cursor:help;"><span class="dashicons ${accessIcon}" style="font-size:16px; width:16px; height:16px;"></span></div>`;

              var actions = "";
              if (isFolder) {
                actions += `<button class="msdl-action-btn msdl-btn-open" data-id="${item.id}" data-name="${item.name}">Megnyitás</button>`;
                if (currentItemType === "folder") {
                  actions += ` <button class="msdl-action-btn msdl-btn-select" data-id="${
                    item.id
                  }" data-name="${
                    item.custom_title || item.name
                  }" data-size="Mappa" data-roles='${roleStr}' data-date="${
                    item.date
                  }">Kiválasztás</button>`;
                }
              } else {
                if (currentItemType === "file") {
                  actions += `<button class="msdl-action-btn msdl-btn-select" data-id="${
                    item.id
                  }" data-name="${item.custom_title || item.name}" data-size="${
                    item.size
                  }" data-roles='${roleStr}' data-date="${
                    item.date
                  }">Kiválasztás</button>`;
                } else {
                  actions += `<span style="color:#a0a6b5; font-size:11px;">Nem választható</span>`;
                }
              }

              var rowStyle =
                currentItemType === "folder" && !isFolder
                  ? "opacity:0.4; filter:grayscale(1);"
                  : "";

              tbody.append(`
                    <tr class="msdl-row-hover" style="border-bottom:1px solid #f0f0f0; ${rowStyle}">
                        <td style="padding:12px 20px; display:flex; align-items:center; overflow:hidden;">${iconHtml} ${titleHtml}</td>
                        <td style="padding:12px 10px; text-align:center;">${descHtml}</td>
                        <td style="padding:12px 10px; text-align:center;">${badgeHtml}</td>
                        <td style="padding:12px 10px; color:#50575e; text-align:right;">${item.size}</td>
                        <td style="padding:12px 10px; color:#50575e;">${item.date}</td>
                        <td style="padding:12px 20px; text-align:right; white-space:nowrap;">${actions}</td>
                    </tr>
                `);
            });
            $(".msdl-items-table").show();
          } else {
            tbody.append(
              '<tr><td colspan="6" style="padding:40px; text-align:center; color:#50575e;">Nincs megjeleníthető elem. (Csak rejtett fájlok találhatóak)</td></tr>',
            );
            $(".msdl-items-table").show();
          }
        } else {
          tbody.append(
            '<tr><td colspan="6" style="padding:40px; text-align:center; color:#50575e;">Nincs megjeleníthető elem.</td></tr>',
          );
          $(".msdl-items-table").show();
        }
      },
    );
  }

  $("body").on("click", ".msdl-open-picker-btn", function (e) {
    e.preventDefault();
    initModal();
    activeWrapper = $(this).closest(".msdl-picker-control-wrapper");
    currentItemType = $(this).data("item-type") || "file";

    breadcrumbs = [{ id: "root", name: "Dokumentumtár (Gyökér)" }];
    currentFolderId = "root";
    currentSearch = "";
    $("#msdl-picker-search").val("");

    $("#msdl-picker-modal").css("display", "flex").hide().fadeIn(200);
    loadItems();
  });

  $("body").on("click", ".msdl-btn-open", function (e) {
    e.preventDefault();
    breadcrumbs.push({ id: $(this).data("id"), name: $(this).data("name") });
    currentFolderId = $(this).data("id");
    currentSearch = "";
    $("#msdl-picker-search").val("");
    loadItems();
  });

  $("body").on("click", ".msdl-bc-link", function (e) {
    e.preventDefault();
    breadcrumbs = breadcrumbs.slice(0, $(this).data("index") + 1);
    currentFolderId = breadcrumbs[breadcrumbs.length - 1].id;
    currentSearch = "";
    $("#msdl-picker-search").val("");
    loadItems();
  });

  var searchTimer;
  $("body").on("input", "#msdl-picker-search", function () {
    clearTimeout(searchTimer);
    currentSearch = $(this).val();
    searchTimer = setTimeout(function () {
      loadItems();
    }, 400);
  });

  $("body").on("click", ".msdl-btn-select", function (e) {
    e.preventDefault();
    var item = $(this).data();
    selectItem(item.id, item.name, item.size, item.roles, item.date);
  });

  $("body").on("click", "#msdl-select-current-folder", function (e) {
    e.preventDefault();
    var id = currentFolderId === "root" ? 0 : currentFolderId;
    var name = breadcrumbs[breadcrumbs.length - 1].name;
    selectItem(id, name, "Mappa", "public", "-");
  });

  function translateRole(roleStr) {
    if (!roleStr || roleStr === "public") return "Nyilvános";
    if (roleStr === "loggedin") return "Bejelentkezett";
    if (roleStr === "hidden") return "Rejtett";
    if (roleStr.includes("[")) {
      try {
        var parsedRoles = JSON.parse(roleStr);
        return parsedRoles
          .map(function (r) {
            return r.charAt(0).toUpperCase() + r.slice(1);
          })
          .join(", ");
      } catch (e) {
        return "Szerepkörhöz kötött";
      }
    }
    return roleStr;
  }

  function selectItem(id, name, size, roles, date) {
    if (activeWrapper) {
      activeWrapper.find(".msdl-picker-hidden-input").val(id).trigger("input");
      var card = activeWrapper.find(".msdl-selected-card");
      card.find(".msdl-sc-name").text(name);
      card.find(".msdl-sc-size").text(size);
      card.find(".msdl-sc-roles").text(translateRole(roles));
      card.find(".msdl-sc-date").text(date);
      card.slideDown("fast");
    }
    $("#msdl-picker-modal").fadeOut(200);
  }

  $("body").on("click", ".msdl-clear-btn", function (e) {
    e.preventDefault();
    var wrapper = $(this).closest(".msdl-picker-control-wrapper");
    wrapper.find(".msdl-picker-hidden-input").val("").trigger("input");
    wrapper.find(".msdl-selected-card").slideUp("fast");
  });

  $("body").on("click", ".msdl-close-modal", function () {
    $("#msdl-picker-modal").fadeOut(200);
  });

  $("body").on("mouseenter", ".msdl-picker-control-wrapper", function () {
    var wrapper = $(this);
    var input = wrapper.find(".msdl-picker-hidden-input");
    var val = input.val();
    var card = wrapper.find(".msdl-selected-card");

    if (val && val !== "0" && !card.hasClass("msdl-loaded")) {
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
            var displayName = response.data.custom_title
              ? response.data.custom_title
              : response.data.name;
            var roleStr = response.data.roles || "public";

            card.find(".msdl-sc-name").text(displayName);
            card.find(".msdl-sc-size").text(response.data.size);
            card.find(".msdl-sc-roles").text(translateRole(roleStr));
            card.find(".msdl-sc-date").text(response.data.date);
            card.show();
          }
        },
      );
    } else if (val === "0" && !card.hasClass("msdl-loaded")) {
      card.addClass("msdl-loaded");
      card.find(".msdl-sc-name").text("Dokumentumtár (Gyökér)");
      card.find(".msdl-sc-size").text("Mappa");
      card.find(".msdl-sc-roles").text("-");
      card.find(".msdl-sc-date").text("-");
      card.show();
    }
  });
});
