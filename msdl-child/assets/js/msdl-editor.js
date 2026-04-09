jQuery(window).on("elementor:init", function () {
  if (!window.elementor) return;

  // ==========================================
  // 1. WIDGET: LETÖLTÉS GOMB (Érintetlen)
  // ==========================================
  elementor.hooks.addAction(
    "panel/open_editor/widget/msdl_button",
    function (panel, model, view) {
      var settings = model.get("settings");
      var isUpdating = false;

      settings.on("change", function (changedModel) {
        if (isUpdating) return;
        var changed = changedModel.changed || {};
        var keys = Object.keys(changed);
        if (keys.length === 0) return;

        if (keys.indexOf("button_template") !== -1) {
          var tpl = changed.button_template;
          if (tpl === "custom") return;
          isUpdating = true;
          var presets = {};

          if (tpl === "tpl-solid")
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
                top: "12",
                right: "24",
                bottom: "12",
                left: "24",
                unit: "px",
                isLinked: false,
              },
            };
          else if (tpl === "tpl-pill")
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
                top: "12",
                right: "28",
                bottom: "12",
                left: "28",
                unit: "px",
                isLinked: false,
              },
            };
          else if (tpl === "tpl-outline")
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
                top: "10",
                right: "22",
                bottom: "10",
                left: "22",
                unit: "px",
                isLinked: false,
              },
            };

          settings.set(presets);
          setTimeout(function () {
            jQuery.each(presets, function (key, val) {
              if (typeof val === "object") {
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
        if (
          keys.some(function (k) {
            return styleKeys.indexOf(k) !== -1;
          }) &&
          settings.get("button_template") !== "custom"
        ) {
          isUpdating = true;
          settings.set("button_template", "custom");
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

  // ==========================================
  // 2. WIDGET: FÁJL INFO KÁRTYA (Új, kreatív adatokkal)
  // ==========================================
  elementor.hooks.addAction(
    "panel/open_editor/widget/msdl_file_card",
    function (panel, model, view) {
      var settings = model.get("settings");
      var isUpdating = false;

      settings.on("change", function (changedModel) {
        if (isUpdating) return;
        var changed = changedModel.changed || {};
        var keys = Object.keys(changed);
        if (keys.length === 0) return;

        if (keys.indexOf("card_template") !== -1) {
          var tpl = changed.card_template;
          if (tpl === "custom") return;

          isUpdating = true;
          var presets = {};

          // 1. Letisztult Vízszintes Lista (Soros, Minimál)
          if (tpl === "tpl-list") {
            presets = {
              layout_style: "row",
              card_bg_color: "rgba(255,255,255,0)",
              card_border_border: "solid",
              card_border_color: "#e2e4e7",
              card_border_width: {
                top: "0",
                right: "0",
                bottom: "1",
                left: "0",
                unit: "px",
                isLinked: false,
              },
              card_border_radius: {
                top: "0",
                right: "0",
                bottom: "0",
                left: "0",
                unit: "px",
                isLinked: true,
              },
              card_padding: {
                top: "20",
                right: "0",
                bottom: "20",
                left: "0",
                unit: "px",
                isLinked: false,
              },
              card_box_shadow_box_shadow_type: "",
              title_color: "#242943",
              meta_color: "#787c82",
              icon_color: "#50ADC9",
              icon_bg_color: "rgba(255,255,255,0)",
              icon_size: { size: 32, unit: "px" },
              icon_padding: {
                top: "0",
                right: "0",
                bottom: "0",
                left: "0",
                unit: "px",
                isLinked: true,
              },
              icon_border_radius: {
                top: "0",
                right: "0",
                bottom: "0",
                left: "0",
                unit: "px",
                isLinked: true,
              },
              btn_bg_color: "rgba(80,173,201,0.1)",
              btn_text_color: "#50ADC9",
              btn_border_border: "",
              btn_border_radius: {
                top: "6",
                right: "6",
                bottom: "6",
                left: "6",
                unit: "px",
                isLinked: true,
              },
              btn_padding: {
                top: "10",
                right: "20",
                bottom: "10",
                left: "20",
                unit: "px",
                isLinked: false,
              },
              btn_hover_bg_color: "#50ADC9",
              btn_hover_text_color: "#ffffff",
            };
          }
          // 2. Kompakt Rács / Lebegő Kártya (Oszlopos, Árnyékos, Ikon háttérrel)
          else if (tpl === "tpl-grid") {
            presets = {
              layout_style: "column",
              card_bg_color: "#ffffff",
              card_border_border: "solid",
              card_border_color: "#f0f2f5",
              card_border_width: {
                top: "1",
                right: "1",
                bottom: "1",
                left: "1",
                unit: "px",
                isLinked: true,
              },
              card_border_radius: {
                top: "16",
                right: "16",
                bottom: "16",
                left: "16",
                unit: "px",
                isLinked: true,
              },
              card_padding: {
                top: "35",
                right: "25",
                bottom: "30",
                left: "25",
                unit: "px",
                isLinked: false,
              },
              card_box_shadow_box_shadow_type: "yes",
              card_box_shadow_box_shadow: {
                horizontal: 0,
                vertical: 10,
                blur: 30,
                spread: 0,
                color: "rgba(36,41,67,0.08)",
              },
              title_color: "#242943",
              meta_color: "#787c82",
              icon_color: "#50ADC9",
              icon_bg_color: "rgba(80,173,201,0.1)",
              icon_size: { size: 28, unit: "px" },
              icon_padding: {
                top: "18",
                right: "18",
                bottom: "18",
                left: "18",
                unit: "px",
                isLinked: true,
              },
              icon_border_radius: {
                top: "50",
                right: "50",
                bottom: "50",
                left: "50",
                unit: "px",
                isLinked: true,
              },
              btn_bg_color: "#242943",
              btn_text_color: "#ffffff",
              btn_border_border: "",
              btn_border_radius: {
                top: "8",
                right: "8",
                bottom: "8",
                left: "8",
                unit: "px",
                isLinked: true,
              },
              btn_padding: {
                top: "12",
                right: "20",
                bottom: "12",
                left: "20",
                unit: "px",
                isLinked: false,
              },
              btn_hover_bg_color: "#50ADC9",
              btn_hover_text_color: "#ffffff",
              btn_hover_animation: "push",
            };
          }
          // 3. Kiemelt CTA Banner (Soros, Sötét, Kontrasztos Gomb)
          else if (tpl === "tpl-cta") {
            presets = {
              layout_style: "row",
              card_bg_color: "#242943",
              card_border_border: "",
              card_border_radius: {
                top: "12",
                right: "12",
                bottom: "12",
                left: "12",
                unit: "px",
                isLinked: true,
              },
              card_padding: {
                top: "30",
                right: "40",
                bottom: "30",
                left: "40",
                unit: "px",
                isLinked: false,
              },
              card_box_shadow_box_shadow_type: "yes",
              card_box_shadow_box_shadow: {
                horizontal: 0,
                vertical: 15,
                blur: 40,
                spread: 0,
                color: "rgba(36,41,67,0.4)",
              },
              title_color: "#ffffff",
              meta_color: "#a0a6b5",
              icon_color: "#ffffff",
              icon_bg_color: "#50ADC9",
              icon_size: { size: 36, unit: "px" },
              icon_padding: {
                top: "16",
                right: "16",
                bottom: "16",
                left: "16",
                unit: "px",
                isLinked: true,
              },
              icon_border_radius: {
                top: "12",
                right: "12",
                bottom: "12",
                left: "12",
                unit: "px",
                isLinked: true,
              },
              btn_bg_color: "rgba(255,255,255,0)",
              btn_text_color: "#ffffff",
              btn_border_border: "solid",
              btn_border_color: "#ffffff",
              btn_border_width: {
                top: "2",
                right: "2",
                bottom: "2",
                left: "2",
                unit: "px",
                isLinked: true,
              },
              btn_border_radius: {
                top: "50",
                right: "50",
                bottom: "50",
                left: "50",
                unit: "px",
                isLinked: true,
              },
              btn_padding: {
                top: "12",
                right: "32",
                bottom: "12",
                left: "32",
                unit: "px",
                isLinked: false,
              },
              btn_hover_bg_color: "#ffffff",
              btn_hover_text_color: "#242943",
              btn_hover_animation: "",
            };
          }

          settings.set(presets);

          setTimeout(function () {
            jQuery.each(presets, function (key, val) {
              if (typeof val === "object") {
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
                if (key === "layout_style") {
                  panel.$el
                    .find(
                      ".elementor-control-" +
                        key +
                        ' input[value="' +
                        val +
                        '"]',
                    )
                    .prop("checked", true);
                } else {
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
              }
            });
            isUpdating = false;
          }, 50);

          return;
        }

        var styleKeys = [
          "layout_style",
          "card_bg_color",
          "card_border_border",
          "card_border_width",
          "card_border_color",
          "card_padding",
          "card_border_radius",
          "card_box_shadow_box_shadow_type",
          "title_color",
          "meta_color",
          "icon_color",
          "icon_bg_color",
          "icon_size",
          "icon_padding",
          "icon_border_radius",
          "btn_bg_color",
          "btn_text_color",
          "btn_border_radius",
          "btn_border_border",
          "btn_border_width",
          "btn_border_color",
          "btn_padding",
          "btn_hover_bg_color",
          "btn_hover_text_color",
          "btn_hover_border_color",
          "btn_hover_animation",
        ];
        var hasStyleChange = keys.some(function (k) {
          return styleKeys.indexOf(k) !== -1;
        });
        var currentTpl = settings.get("card_template");

        if (hasStyleChange && currentTpl !== "custom") {
          isUpdating = true;
          settings.set("card_template", "custom");
          setTimeout(function () {
            panel.$el
              .find(".elementor-control-card_template select")
              .val("custom");
            isUpdating = false;
          }, 10);
        }
      });
    },
  );
});
