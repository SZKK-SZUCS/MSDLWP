jQuery(window).on("elementor:init", function () {
  if (!window.elementor) return;

  elementor.hooks.addAction(
    "panel/open_editor/widget/msdl_button",
    function (panel, model, view) {
      var settings = model.get("settings");
      var isUpdating = false;

      settings.on("change", function (changedModel) {
        // Ha épp a makró dolgozik, ignoráljuk a visszajelzéseket
        if (isUpdating) return;

        var changed = changedModel.changed || {};
        var keys = Object.keys(changed);
        if (keys.length === 0) return;

        // --- 1. ESEMÉNY: SABLON VÁLTÁS ---
        if (keys.indexOf("button_template") !== -1) {
          var tpl = changed.button_template;
          if (tpl === "custom") return;

          isUpdating = true;

          var presets = {};
          if (tpl === "tpl-solid") {
            presets = {
              bg_color: "#242943",
              hover_bg_color: "#50ADC9",
              text_color: "#ffffff",
              icon_color: "#ffffff",
              hover_text_color: "#ffffff",
              hover_icon_color: "#ffffff",
              border_border: "",
              border_radius: {
                top: "4",
                right: "4",
                bottom: "4",
                left: "4",
                unit: "px",
                isLinked: true,
              },
              padding: {
                top: "10",
                right: "20",
                bottom: "10",
                left: "20",
                unit: "px",
                isLinked: false,
              },
            };
          } else if (tpl === "tpl-pill") {
            presets = {
              bg_color: "#50ADC9",
              hover_bg_color: "#242943",
              text_color: "#ffffff",
              icon_color: "#ffffff",
              hover_text_color: "#ffffff",
              hover_icon_color: "#ffffff",
              border_border: "",
              border_radius: {
                top: "50",
                right: "50",
                bottom: "50",
                left: "50",
                unit: "px",
                isLinked: true,
              },
              padding: {
                top: "10",
                right: "22",
                bottom: "10",
                left: "22",
                unit: "px",
                isLinked: false,
              },
            };
          } else if (tpl === "tpl-outline") {
            presets = {
              bg_color: "rgba(255,255,255,0)",
              hover_bg_color: "#242943",
              text_color: "#242943",
              icon_color: "#242943",
              hover_text_color: "#ffffff",
              hover_icon_color: "#ffffff",
              border_border: "solid",
              border_color: "#242943",
              border_width: {
                top: "2",
                right: "2",
                bottom: "2",
                left: "2",
                unit: "px",
                isLinked: true,
              },
              border_radius: {
                top: "4",
                right: "4",
                bottom: "4",
                left: "4",
                unit: "px",
                isLinked: true,
              },
              padding: {
                top: "9",
                right: "19",
                bottom: "9",
                left: "19",
                unit: "px",
                isLinked: false,
              },
            };
          }

          // LÉPÉS 1: Adatmodell frissítése (Ettől frissül a Live Preview a weboldalon)
          settings.set(presets);

          // LÉPÉS 2: Bal oldali panel mezőinek finom, manuális felülírása (Nincs Render = Nincs Tab fagyás!)
          setTimeout(function () {
            jQuery.each(presets, function (key, val) {
              if (typeof val === "object") {
                // Méretek (padding, radius, border_width)
                jQuery.each(val, function (dim, dimVal) {
                  panel.$el
                    .find(
                      ".elementor-control-" +
                        key +
                        ' input[data-setting="' +
                        dim +
                        '"]',
                    )
                    .val(dimVal);
                });
              } else {
                // Szöveges mezők, legördülők, színválasztók
                panel.$el
                  .find(
                    ".elementor-control-" +
                      key +
                      ' input[type="text"], .elementor-control-' +
                      key +
                      " select",
                  )
                  .val(val);
              }
            });
            isUpdating = false;
          }, 50);

          return;
        }

        // --- 2. ESEMÉNY: MANUÁLIS MÓDOSÍTÁS ---
        var styleKeys = [
          "bg_color",
          "hover_bg_color",
          "text_color",
          "icon_color",
          "hover_text_color",
          "hover_icon_color",
          "border_radius",
          "padding",
          "border_border",
          "border_width",
          "border_color",
        ];
        var hasStyleChange = keys.some(function (k) {
          return styleKeys.indexOf(k) !== -1;
        });
        var currentTpl = settings.get("button_template");

        if (hasStyleChange && currentTpl !== "custom") {
          isUpdating = true;

          // Modell átállítása Custom-re
          settings.set("button_template", "custom");

          // Legördülő menü vizuális átállítása
          setTimeout(function () {
            panel.$el
              .find(".elementor-control-button_template select")
              .val("custom");
            isUpdating = false;
          }, 10);
        }
      });
    },
  );
});
