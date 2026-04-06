(function () {
  function replaceTokens(markup, group, index) {
    return markup
      .replace(/__GROUP__/g, group)
      .replace(/__INDEX__/g, String(index));
  }

  function nextIndex(list) {
    return list.querySelectorAll("[data-hex-settings-row]").length;
  }

  function addRow(listKey) {
    var list = document.querySelector('[data-hex-settings-list="' + listKey + '"]');
    if (!list) {
      return;
    }

    var templateId = listKey === "transfer-locations"
      ? "hex-settings-row-template-location"
      : "hex-settings-row-template-basic";
    var template = document.getElementById(templateId);
    if (!template) {
      return;
    }

    var group = listKey === "sales-channels"
      ? "sales_channels"
      : (listKey === "equipment-options" ? "equipment_options" : "transfer_locations");

    var wrapper = document.createElement("div");
    wrapper.innerHTML = replaceTokens(template.innerHTML, group, nextIndex(list));

    var row = wrapper.firstElementChild;
    if (!row) {
      return;
    }

    list.appendChild(row);

    var firstInput = row.querySelector("input");
    if (firstInput) {
      firstInput.focus();
    }
  }

  function removeRow(button) {
    var row = button.closest("[data-hex-settings-row]");
    var list = row ? row.parentElement : null;

    if (!row || !list) {
      return;
    }

    if (list.querySelectorAll("[data-hex-settings-row]").length <= 1) {
      var inputs = row.querySelectorAll("input");
      inputs.forEach(function (input) {
        input.value = "";
      });
      var firstInput = row.querySelector("input");
      if (firstInput) {
        firstInput.focus();
      }
      return;
    }

    row.remove();
  }

  function bind() {
    document.querySelectorAll("[data-hex-settings-add]").forEach(function (button) {
      button.addEventListener("click", function () {
        addRow(button.getAttribute("data-hex-settings-add"));
      });
    });

    document.addEventListener("click", function (event) {
      var removeButton = event.target.closest("[data-hex-settings-remove]");
      if (removeButton) {
        removeRow(removeButton);
      }
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bind);
  } else {
    bind();
  }
})();
