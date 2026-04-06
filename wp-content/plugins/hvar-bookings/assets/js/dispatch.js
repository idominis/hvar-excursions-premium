(function () {
  var state = {
    currentUser: null,
    canManage: false,
    resources: [],
    bookers: [],
    salesChannels: [],
    equipmentOptions: [],
    transferLocations: [],
    notificationSettings: {},
    todayEvents: [],
    myEvents: [],
    currentBookingId: null,
    currentConfirmationPreview: null,
    currentManagerNotification: null,
    currentScreen: "today",
    todayFilters: {
      scope: "all",
      status: "",
      service_type: "",
    },
  };

  function $(selector) {
    return document.querySelector(selector);
  }

  function dispatchForm() {
    return $("#hex-dispatch-form");
  }

  function isTransferService(serviceType) {
    return serviceType === "transfer" || serviceType === "taxi";
  }

  function escapeHtml(value) {
    return String(value == null ? "" : value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function titleCase(value) {
    return String(value || "")
      .replace(/_/g, " ")
      .replace(/-/g, " ")
      .replace(/\b\w/g, function (char) {
        return char.toUpperCase();
      });
  }

  function todayValue() {
    return toDateValue(new Date());
  }

  function toDateValue(date) {
    if (!(date instanceof Date)) {
      date = new Date(date);
    }

    return [
      date.getFullYear(),
      String(date.getMonth() + 1).padStart(2, "0"),
      String(date.getDate()).padStart(2, "0"),
    ].join("-");
  }

  function addDays(date, days) {
    var next = new Date(date.getTime());
    next.setDate(next.getDate() + days);
    return next;
  }

  async function request(path, options) {
    var response = await fetch(hexBookingsConfig.restUrl + path, Object.assign({
      headers: {
        "X-WP-Nonce": hexBookingsConfig.nonce,
        "Content-Type": "application/json",
      },
      credentials: "same-origin",
    }, options || {}));

    var contentType = response.headers.get("content-type") || "";
    var body = contentType.indexOf("application/json") !== -1 ? await response.json() : await response.text();

    if (!response.ok) {
      var message = body && body.message ? body.message : ("Request failed: " + response.status);
      var error = new Error(message);
      error.response = body;
      throw error;
    }

    return body;
  }

  function setToast(message, type) {
    var node = $("#hex-dispatch-toast");
    if (!node) {
      return;
    }

    if (!message) {
      node.hidden = true;
      node.textContent = "";
      node.classList.remove("is-error", "is-success");
      return;
    }

    node.textContent = message;
    node.hidden = false;
    node.classList.remove("is-error", "is-success");
    node.classList.add(type === "error" ? "is-error" : "is-success");
  }

  function setFormMessage(message, type) {
    var node = $("#hex-dispatch-form-message");
    if (!node) {
      return;
    }

    node.textContent = message || "";
    node.classList.remove("is-error", "is-success");
    if (type) {
      node.classList.add(type === "error" ? "is-error" : "is-success");
    }
  }

  function setFormStatus(confirmation, managerNotification) {
    var panel = $("#hex-dispatch-form-status");
    if (!panel) {
      return;
    }

    var confirmationRow = panel.querySelector('[data-status-kind="confirmation"]');
    var confirmationText = panel.querySelector('[data-status-text="confirmation"]');
    var confirmationButton = panel.querySelector("[data-status-send-confirmation]");
    var managerRow = panel.querySelector('[data-status-kind="manager"]');
    var managerText = panel.querySelector('[data-status-text="manager"]');

    var hasConfirmation = !!(confirmation && (confirmation.enabled || confirmation.subject || confirmation.message));
    var hasManager = !!(managerNotification && managerNotification.enabled);

    if (confirmationRow) {
      confirmationRow.hidden = !hasConfirmation;
    }
    if (managerRow) {
      managerRow.hidden = !hasManager;
    }

    if (confirmationText) {
      if (!hasConfirmation) {
        confirmationText.textContent = "";
      } else if (confirmation.sent && confirmation.recipient) {
        confirmationText.textContent = "Confirmation sent to " + confirmation.recipient;
      } else if (confirmation.enabled && confirmation.recipient) {
        confirmationText.textContent = "Confirmation ready for " + confirmation.recipient;
      } else if (confirmation.message) {
        confirmationText.textContent = confirmation.message;
      } else {
        confirmationText.textContent = "Confirmation prepared.";
      }
    }

    if (confirmationButton) {
      confirmationButton.hidden = !(hasConfirmation && state.currentBookingId && confirmation && confirmation.enabled);
    }

    if (managerText) {
      if (!hasManager) {
        managerText.textContent = "";
      } else if (managerNotification.scheduled_time) {
        managerText.textContent = "Manager note ready for Filip at " + managerNotification.scheduled_time;
      } else {
        managerText.textContent = "Manager note ready for Filip.";
      }
    }

    panel.hidden = !(hasConfirmation || hasManager);
  }

  function formatDate(dateValue) {
    return new Date(dateValue).toLocaleDateString(undefined, {
      weekday: "short",
      day: "numeric",
      month: "short",
      year: "numeric",
    });
  }

  function formatEventTimeRange(event) {
    if (event.allDay) {
      return "All day";
    }

    var start = new Date(event.start);
    var end = new Date(event.end || event.start);

    return start.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }) +
      " - " +
      end.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  }

  function getResourceById(resourceId) {
    var id = Number(resourceId);
    return state.resources.find(function (resource) {
      return Number(resource.id) === id;
    }) || null;
  }

  function getGroupedResources() {
    return state.resources.reduce(function (groups, resource) {
      if (!groups[resource.category]) {
        groups[resource.category] = [];
      }
      groups[resource.category].push(resource);
      return groups;
    }, {});
  }

  function populateUserCard() {
    var user = state.currentUser || {};
    var card = $("#hex-dispatch-usercard");
    if (!card) {
      return;
    }

    card.querySelector(".hex-dispatch-usercard__badge").textContent = user.initials || "--";
    card.querySelector(".hex-dispatch-usercard__name").textContent = user.display_name || "Unknown user";
    card.querySelector(".hex-dispatch-usercard__email").textContent = user.email || "";
    $("#hex-dispatch-profile-name").textContent = user.display_name || "Unknown user";
    $("#hex-dispatch-profile-email").textContent = user.email || "";
  }

  function populateTodayLabel() {
    var node = $("#hex-dispatch-today-label");
    if (node) {
      node.textContent = formatDate(new Date());
    }
  }

  function updateCounters() {
    $("#hex-dispatch-today-count").textContent = String(state.todayEvents.length);
    $("#hex-dispatch-mine-count").textContent = String(state.myEvents.length);
  }

  function resourceOptionsMarkup() {
    return state.resources.map(function (resource) {
      return '<option value="' + escapeHtml(resource.id) + '">' +
        escapeHtml(resource.name + " (" + titleCase(resource.category) + ")") +
      "</option>";
    }).join("");
  }

  function salesChannelOptionsMarkup() {
    return state.salesChannels.map(function (item) {
      return '<option value="' + escapeHtml(item.value) + '">' + escapeHtml(item.label) + "</option>";
    }).join("");
  }

  function equipmentOptionsMarkup() {
    return state.equipmentOptions.map(function (item) {
      return '<option value="' + escapeHtml(item.value) + '">' + escapeHtml(item.label) + "</option>";
    }).join("");
  }

  function transferLocationOptionsMarkup() {
    return ['<option value="">' + escapeHtml("Choose location") + "</option>"]
      .concat(
        state.transferLocations.map(function (item) {
          return '<option value="' + escapeHtml(item.value) + '" data-label="' + escapeHtml(item.label) + '" data-coordinates="' + escapeHtml(item.coordinates || "") + '">' + escapeHtml(item.label) + "</option>";
        })
      )
      .concat('<option value="custom">' + escapeHtml("Custom") + "</option>")
      .join("");
  }

  function salesChannelLabel(value) {
    var match = state.salesChannels.find(function (item) {
      return item.value === value;
    });
    return match ? match.label : titleCase(String(value || "cash").replace(/_/g, " "));
  }

  function equipmentLabels(values) {
    if (!Array.isArray(values) || !values.length) {
      return [];
    }

    return values.map(function (value) {
      var match = state.equipmentOptions.find(function (item) {
        return item.value === value;
      });
      return match ? match.label : titleCase(String(value).replace(/_/g, " "));
    });
  }

  function getSelectOptionData(select) {
    if (!select) {
      return { value: "", label: "", coordinates: "" };
    }

    var option = select.options[select.selectedIndex];
    if (!option) {
      return { value: "", label: "", coordinates: "" };
    }

    return {
      value: option.value || "",
      label: option.getAttribute("data-label") || option.textContent || "",
      coordinates: option.getAttribute("data-coordinates") || "",
    };
  }

  function populateBookAsOptions() {
    var form = dispatchForm();
    if (!form || !form.elements.book_as_user_id) {
      return;
    }

    var wrapper = form.elements.book_as_user_id.closest(".hex-dispatch-form__book-as");
    if (!state.canManage) {
      wrapper.hidden = true;
      form.elements.book_as_user_id.innerHTML = "";
      return;
    }

    form.elements.book_as_user_id.innerHTML = state.bookers.map(function (booker) {
      var label = (booker.initials ? "[" + booker.initials + "] " : "") + booker.display_name;
      return '<option value="' + escapeHtml(booker.id) + '">' + escapeHtml(label) + "</option>";
    }).join("");
    form.elements.book_as_user_id.value = String(state.currentUser.id || "");
    wrapper.hidden = false;
  }

  function populateFormSelects() {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    form.elements.resource_id.innerHTML = resourceOptionsMarkup();
    if (form.elements.sales_channel) {
      form.elements.sales_channel.innerHTML = '<option value="">' + escapeHtml("None") + "</option>" + salesChannelOptionsMarkup();
    }
    if (form.elements.extra_equipment) {
      form.elements.extra_equipment.innerHTML = '<option value="">' + escapeHtml("None") + "</option>" + equipmentOptionsMarkup();
    }
    if (form.elements.pickup_location) {
      form.elements.pickup_location.innerHTML = transferLocationOptionsMarkup();
    }
    if (form.elements.dropoff_location) {
      form.elements.dropoff_location.innerHTML = transferLocationOptionsMarkup();
    }
    populateBookAsOptions();
  }

  function setMultiSelectValues(select, values) {
    if (!select) {
      return;
    }

    var list = Array.isArray(values) ? values.map(String) : [];
    Array.prototype.forEach.call(select.options, function (option) {
      option.selected = list.indexOf(String(option.value)) !== -1;
    });
  }

  function getMultiSelectValues(select) {
    if (!select) {
      return [];
    }

    return Array.prototype.filter.call(select.options, function (option) {
      return option.selected;
    }).map(function (option) {
      return option.value;
    });
  }

  function syncSkipperRules() {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    var resource = getResourceById(form.elements.resource_id.value);
    var serviceType = form.elements.service_type.value;
    var forceSkipper = serviceType === "transfer" || serviceType === "taxi";
    var bareboatAllowed = !resource || Number(resource.supports_bareboat) === 1;

    Array.prototype.forEach.call(form.elements.skipper_mode.options, function (option) {
      option.disabled = false;
    });

    if (forceSkipper || !bareboatAllowed) {
      form.elements.skipper_mode.value = "with_skipper";
      var withoutSkipper = form.elements.skipper_mode.querySelector('option[value="without_skipper"]');
      if (withoutSkipper) {
        withoutSkipper.disabled = true;
      }
    }
  }

  function syncServiceModeUI() {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    var transferMode = isTransferService(form.elements.service_type.value);

    form.querySelectorAll(".hex-dispatch-form__mode").forEach(function (node) {
      var show = node.classList.contains("hex-dispatch-form__mode--transfer") ? transferMode : !transferMode;
      node.hidden = !show;
    });

    var notesLabel = form.querySelector(".hex-dispatch-form__notes span");
    var notesField = form.elements.notes;
    if (notesLabel && notesField) {
      notesLabel.textContent = transferMode
        ? notesLabel.getAttribute("data-transfer-label")
        : notesLabel.getAttribute("data-rental-label");
      notesField.placeholder = transferMode
        ? notesField.getAttribute("data-transfer-placeholder")
        : notesField.getAttribute("data-rental-placeholder");
    }

    syncTransferCustomFields();
  }

  function syncTransferCustomFields() {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    var transferMode = isTransferService(form.elements.service_type.value);
    var pickupCustom = transferMode && form.elements.pickup_location && form.elements.pickup_location.value === "custom";
    var dropoffCustom = transferMode && form.elements.dropoff_location && form.elements.dropoff_location.value === "custom";

    var pickupWrapper = form.querySelector('[data-hex-custom-coords="pickup"]');
    var dropoffWrapper = form.querySelector('[data-hex-custom-coords="dropoff"]');

    if (pickupWrapper) {
      pickupWrapper.hidden = !pickupCustom;
    }
    if (dropoffWrapper) {
      dropoffWrapper.hidden = !dropoffCustom;
    }
  }

  function openMapTarget(kind) {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    var query = "";
    if (kind === "pickup") {
      var pickup = getSelectOptionData(form.elements.pickup_location);
      query = pickup.coordinates || pickup.label;
    } else if (kind === "dropoff") {
      var dropoff = getSelectOptionData(form.elements.dropoff_location);
      query = dropoff.coordinates || dropoff.label;
    } else if (kind === "pickup-custom") {
      query = form.elements.pickup_coordinates.value;
    } else if (kind === "dropoff-custom") {
      query = form.elements.dropoff_coordinates.value;
    }

    query = String(query || "").trim();
    if (!query) {
      setToast("No location or coordinates to open on the map.", "error");
      return;
    }

    window.open("https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(query), "_blank", "noopener");
  }

  async function copyText(text) {
    var value = String(text || "");
    if (!value) {
      return false;
    }

    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(value);
        return true;
      }
    } catch (error) {
      // fall through to textarea fallback
    }

    var textarea = document.createElement("textarea");
    textarea.value = value;
    textarea.setAttribute("readonly", "readonly");
    textarea.style.position = "fixed";
    textarea.style.left = "-9999px";
    textarea.style.top = "0";
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    var copied = false;
    try {
      copied = document.execCommand("copy");
    } catch (error) {
      copied = false;
    }

    document.body.removeChild(textarea);
    return copied;
  }

  function openPreview(title, eyebrow, text) {
    var preview = $("#hex-dispatch-preview");
    if (!preview) {
      return;
    }

    $("#hex-dispatch-preview-title").textContent = title || "Preview";
    $("#hex-dispatch-preview-eyebrow").textContent = eyebrow || "";
    $("#hex-dispatch-preview-text").textContent = text || "";
    preview.hidden = false;
    document.body.classList.add("hex-dispatch-preview-open");
  }

  function closePreview() {
    var preview = $("#hex-dispatch-preview");
    if (!preview) {
      return;
    }

    preview.hidden = true;
    document.body.classList.remove("hex-dispatch-preview-open");
  }

  function toggleTimeFields() {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    var disabled = !!form.elements.is_all_day.checked;
    form.elements.start_time.disabled = disabled;
    form.elements.end_time.disabled = disabled;
  }

  function resetForm(prefill) {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    state.currentBookingId = null;
    form.reset();
    form.elements.booking_id.value = "";
    form.elements.resource_id.value = state.resources.length ? String(state.resources[0].id) : "";
    form.elements.booking_date.value = todayValue();
    form.elements.start_time.value = "09:00";
    form.elements.end_time.value = "17:00";
    form.elements.status.value = "draft";
    form.elements.service_type.value = "rental";
    form.elements.skipper_mode.value = "with_skipper";
    if (form.elements.fuel_included) {
      form.elements.fuel_included.checked = false;
    }
    if (form.elements.generate_confirmation) {
      form.elements.generate_confirmation.checked = false;
    }
    if (form.elements.generate_manager_notification) {
      form.elements.generate_manager_notification.checked = false;
    }
    if (form.elements.sales_channel) {
      form.elements.sales_channel.value = "cash";
    }
    if (form.elements.extra_equipment) {
      form.elements.extra_equipment.value = "";
    }
    if (form.elements.pickup_location) {
      form.elements.pickup_location.value = "";
    }
    if (form.elements.dropoff_location) {
      form.elements.dropoff_location.value = "";
    }
    if (form.elements.pickup_coordinates) {
      form.elements.pickup_coordinates.value = "";
    }
    if (form.elements.dropoff_coordinates) {
      form.elements.dropoff_coordinates.value = "";
    }
    if (state.canManage && form.elements.book_as_user_id) {
      form.elements.book_as_user_id.value = String(state.currentUser.id || "");
    }
    $("[data-dispatch-cancel-booking]").hidden = true;
    $("#hex-dispatch-form-title").textContent = "New Booking";

    if (prefill) {
      Object.keys(prefill).forEach(function (key) {
        var field = form.elements[key];
        if (!field) {
          return;
        }
        if (field.type === "checkbox") {
          field.checked = !!prefill[key];
        } else {
          field.value = prefill[key] == null ? "" : String(prefill[key]);
        }
      });
    }

    toggleTimeFields();
    syncServiceModeUI();
    syncSkipperRules();
    setFormMessage("");
    setFormStatus(null, null);
  }

  function populateFormFromBooking(booking) {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    state.currentBookingId = Number(booking.id);
    form.elements.booking_id.value = String(booking.id);
    form.elements.resource_id.value = String(booking.resource_id);
    form.elements.booking_date.value = booking.booking_date;
    form.elements.start_time.value = booking.start_time ? booking.start_time.slice(0, 5) : "";
    form.elements.end_time.value = booking.end_time ? booking.end_time.slice(0, 5) : "";
    form.elements.is_all_day.checked = Number(booking.is_all_day) === 1;
    form.elements.status.value = booking.status;
    form.elements.service_type.value = booking.service_type;
    form.elements.skipper_mode.value = booking.skipper_mode;
    if (form.elements.fuel_included) {
      form.elements.fuel_included.checked = !!booking.fuel_included;
    }
    if (form.elements.generate_confirmation) {
      form.elements.generate_confirmation.checked = !!booking.generate_confirmation;
    }
    if (form.elements.generate_manager_notification) {
      form.elements.generate_manager_notification.checked = !!booking.generate_manager_notification;
    }
    if (state.canManage && form.elements.book_as_user_id) {
      form.elements.book_as_user_id.value = String(booking.booker_user_id || state.currentUser.id || "");
    }
    form.elements.customer_name.value = booking.customer_name || "";
    form.elements.customer_phone.value = booking.customer_phone || "";
    form.elements.customer_email.value = booking.customer_email || "";
    form.elements.route_summary.value = booking.route_summary || "";
    if (form.elements.pickup_location) {
      var pickupMatch = Array.prototype.find.call(form.elements.pickup_location.options, function (option) {
        return option.getAttribute("data-label") === (booking.pickup_location || "");
      });
      form.elements.pickup_location.value = pickupMatch ? pickupMatch.value : (booking.pickup_coordinates ? "custom" : "");
    }
    if (form.elements.dropoff_location) {
      var dropoffMatch = Array.prototype.find.call(form.elements.dropoff_location.options, function (option) {
        return option.getAttribute("data-label") === (booking.dropoff_location || "");
      });
      form.elements.dropoff_location.value = dropoffMatch ? dropoffMatch.value : (booking.dropoff_coordinates ? "custom" : "");
    }
    form.elements.luggage_details.value = booking.luggage_details || "";
    form.elements.pickup_coordinates.value = booking.pickup_coordinates || "";
    form.elements.dropoff_coordinates.value = booking.dropoff_coordinates || "";
    form.elements.passengers.value = booking.passengers || "";
    form.elements.booking_price.value = booking.booking_price == null ? "" : booking.booking_price;
    form.elements.advance_amount.value = booking.advance_amount == null ? "" : booking.advance_amount;
    form.elements.sales_channel.value = booking.sales_channel || "cash";
    form.elements.notes.value = booking.notes || "";
    form.elements.internal_notes.value = booking.internal_notes || "";
    if (form.elements.extra_equipment) {
      form.elements.extra_equipment.value = Array.isArray(booking.extra_equipment) && booking.extra_equipment.length ? booking.extra_equipment[0] : "";
    }
    $("#hex-dispatch-form-title").textContent = "Edit Booking";
    $("[data-dispatch-cancel-booking]").hidden = false;

    toggleTimeFields();
    syncServiceModeUI();
    syncSkipperRules();
    setFormMessage("Editing booking #" + booking.id, "success");
  }

  function buildPayloadFromForm() {
    var form = dispatchForm();
    var transferMode = isTransferService(form.elements.service_type.value);
    var pickupData = getSelectOptionData(form.elements.pickup_location);
    var dropoffData = getSelectOptionData(form.elements.dropoff_location);
    var routeSummary = form.elements.route_summary.value;

    if (transferMode && !routeSummary) {
      routeSummary = [
        pickupData.value === "custom" ? "Custom" : pickupData.label,
        dropoffData.value === "custom" ? "Custom" : dropoffData.label
      ]
        .filter(Boolean)
        .join(" -> ");
    }

    var payload = {
      resource_id: Number(form.elements.resource_id.value),
      booking_date: form.elements.booking_date.value,
      start_time: form.elements.start_time.value,
      end_time: form.elements.end_time.value,
      is_all_day: form.elements.is_all_day.checked,
      status: form.elements.status.value,
      service_type: form.elements.service_type.value,
      skipper_mode: form.elements.skipper_mode.value,
      customer_name: form.elements.customer_name.value,
      customer_phone: form.elements.customer_phone.value,
      customer_email: form.elements.customer_email.value,
      route_summary: routeSummary,
      pickup_location: pickupData.value === "custom" ? "Custom" : pickupData.label,
      dropoff_location: dropoffData.value === "custom" ? "Custom" : dropoffData.label,
      luggage_details: form.elements.luggage_details.value,
      pickup_coordinates: pickupData.value === "custom" ? form.elements.pickup_coordinates.value : pickupData.coordinates,
      dropoff_coordinates: dropoffData.value === "custom" ? form.elements.dropoff_coordinates.value : dropoffData.coordinates,
      passengers: form.elements.passengers.value ? Number(form.elements.passengers.value) : 0,
      booking_price: form.elements.booking_price.value,
      advance_amount: form.elements.advance_amount.value,
      sales_channel: form.elements.sales_channel.value,
      extra_equipment: form.elements.extra_equipment && form.elements.extra_equipment.value ? [form.elements.extra_equipment.value] : [],
      fuel_included: form.elements.fuel_included ? !!form.elements.fuel_included.checked : false,
      generate_confirmation: form.elements.generate_confirmation ? !!form.elements.generate_confirmation.checked : false,
      generate_manager_notification: form.elements.generate_manager_notification ? !!form.elements.generate_manager_notification.checked : false,
      notes: form.elements.notes.value,
      internal_notes: form.elements.internal_notes.value,
      source: "wp_internal",
    };

    if (state.canManage && form.elements.book_as_user_id && form.elements.book_as_user_id.value) {
      payload.book_as_user_id = Number(form.elements.book_as_user_id.value);
    }

    return payload;
  }

  function showScreen(screenName) {
    state.currentScreen = screenName;
    document.querySelectorAll(".hex-dispatch-view").forEach(function (screen) {
      screen.classList.toggle("is-active", screen.getAttribute("data-screen") === screenName);
    });
    document.querySelectorAll(".hex-dispatch-nav__item").forEach(function (button) {
      button.classList.toggle("is-active", button.getAttribute("data-screen-target") === screenName);
    });
  }

  function renderEmptyState(container, title, body) {
    container.innerHTML =
      '<div class="hex-dispatch-empty">' +
      '<strong>' + escapeHtml(title) + "</strong>" +
      '<p>' + escapeHtml(body) + "</p>" +
      "</div>";
  }

  function buildCardMarkup(event, options) {
    options = options || {};
    var resource = getResourceById(event.resourceId);
    var startLabel = options.showDate ? formatDate(event.start) + " • " + formatEventTimeRange(event) : formatEventTimeRange(event);
    var serviceLabel = titleCase(event.extendedProps.service_type || "");
    var title = event.title || "Booking";
    var priceBadge = event.extendedProps.booking_price != null
      ? '<span class="hex-dispatch-card__pill">' + escapeHtml("EUR " + Number(event.extendedProps.booking_price).toFixed(2)) + "</span>"
      : "";

    return (
      '<button type="button" class="hex-dispatch-card" data-booking-id="' + escapeHtml(event.id) + '">' +
        '<div class="hex-dispatch-card__top">' +
          '<span class="hex-dispatch-card__boat">' + escapeHtml(resource ? resource.name : "Boat") + "</span>" +
          '<span class="hex-dispatch-card__time">' + escapeHtml(startLabel) + "</span>" +
        "</div>" +
        '<div class="hex-dispatch-card__title">' + escapeHtml(title) + "</div>" +
        '<div class="hex-dispatch-card__meta">' +
          '<span class="hex-dispatch-card__pill">' + escapeHtml(serviceLabel || "Booking") + "</span>" +
          '<span class="hex-dispatch-card__pill is-accent">' + escapeHtml(event.extendedProps.badge || "--") + "</span>" +
          priceBadge +
          '<span class="hex-dispatch-card__initials">' + escapeHtml(event.extendedProps.booker_initials || "--") + "</span>" +
        "</div>" +
      "</button>"
    );
  }

  function renderTodayList() {
    var container = $("#hex-dispatch-today-list");
    if (!container) {
      return;
    }

    var events = state.todayEvents.filter(function (event) {
      if (state.todayFilters.scope === "mine" && Number(event.extendedProps.booker_user_id) !== Number(state.currentUser.id)) {
        return false;
      }
      if (state.todayFilters.status && event.extendedProps.status !== state.todayFilters.status) {
        return false;
      }
      if (state.todayFilters.service_type && event.extendedProps.service_type !== state.todayFilters.service_type) {
        return false;
      }
      return true;
    });

    if (!events.length) {
      renderEmptyState(container, "No bookings", "Nothing matches the current today filters.");
      return;
    }

    container.innerHTML = events.map(function (event) {
      return buildCardMarkup(event);
    }).join("");
  }

  function renderMyList() {
    var container = $("#hex-dispatch-my-list");
    if (!container) {
      return;
    }

    if (!state.myEvents.length) {
      renderEmptyState(container, "No upcoming bookings", "Your next bookings will appear here.");
      return;
    }

    container.innerHTML = state.myEvents.map(function (event) {
      return buildCardMarkup(event, { showDate: true });
    }).join("");
  }

  function renderBoatGroups() {
    var container = $("#hex-dispatch-boatgroups");
    if (!container) {
      return;
    }

    var grouped = getGroupedResources();
    var todayCountByResource = state.todayEvents.reduce(function (carry, event) {
      var resourceId = String(event.resourceId);
      carry[resourceId] = (carry[resourceId] || 0) + 1;
      return carry;
    }, {});

    container.innerHTML = Object.keys(grouped).map(function (category) {
      return (
        '<section class="hex-dispatch-boatgroup">' +
          '<h3>' + escapeHtml(titleCase(category)) + "</h3>" +
          '<div class="hex-dispatch-boatgroup__grid">' +
            grouped[category].map(function (resource) {
              var count = todayCountByResource[String(resource.id)] || 0;
              var status = count === 0 ? "Free today" : (count === 1 ? "1 booking today" : count + " bookings today");
              return (
                '<button type="button" class="hex-dispatch-boatcard" data-resource-id="' + escapeHtml(resource.id) + '">' +
                  '<strong>' + escapeHtml(resource.name) + "</strong>" +
                  '<span>' + escapeHtml(resource.capacity ? resource.capacity + " pax" : "Capacity on request") + "</span>" +
                  '<em>' + escapeHtml(status) + "</em>" +
                "</button>"
              );
            }).join("") +
          "</div>" +
        "</section>"
      );
    }).join("");
  }

  async function loadTodayEvents() {
    var today = todayValue();
    var payload = await request("bookings?date_from=" + encodeURIComponent(today) + "&date_to=" + encodeURIComponent(today), { method: "GET" });
    state.todayEvents = payload.events || [];
    renderTodayList();
    renderBoatGroups();
    updateCounters();
  }

  async function loadMyEvents() {
    var today = new Date();
    var payload = await request(
      "bookings?date_from=" + encodeURIComponent(toDateValue(today)) +
      "&date_to=" + encodeURIComponent(toDateValue(addDays(today, 14))) +
      "&only_mine=1",
      { method: "GET" }
    );
    state.myEvents = payload.events || [];
    renderMyList();
    updateCounters();
  }

  function setActiveChip(filter, value) {
    if (filter === "scope") {
      state.todayFilters.scope = value;
    } else {
      state.todayFilters[filter] = state.todayFilters[filter] === value ? "" : value;
    }

    document.querySelectorAll("#hex-dispatch-today-filters .hex-dispatch-chip").forEach(function (chip) {
      var chipFilter = chip.getAttribute("data-filter");
      var chipValue = chip.getAttribute("data-value");
      var active = chipFilter === "scope"
        ? state.todayFilters.scope === chipValue
        : state.todayFilters[chipFilter] === chipValue;
      chip.classList.toggle("is-active", active);
    });

    renderTodayList();
  }

  function openDrawerWithBooking(booking, managerNotification, confirmationPreview) {
    var drawer = $("#hex-dispatch-drawer");
    if (!drawer) {
      return;
    }

    drawer.querySelector(".hex-dispatch-drawer__title").textContent = booking.customer_name || booking.route_summary || ("Booking #" + booking.id);
    drawer.querySelector(".hex-dispatch-drawer__badges").innerHTML =
      '<span class="hex-dispatch-card__pill">' + escapeHtml(titleCase(booking.service_type)) + "</span>" +
      '<span class="hex-dispatch-card__pill is-accent">' + escapeHtml(titleCase((booking.skipper_mode || "").replace(/_/g, " "))) + "</span>" +
      '<span class="hex-dispatch-card__initials">' + escapeHtml(booking.booker_initials || "--") + "</span>";

    var resource = getResourceById(booking.resource_id);
    var whenLabel = booking.is_all_day ? (formatDate(booking.booking_date) + " • All day") : (
      formatDate(booking.booking_date) + " • " +
      (booking.start_time ? booking.start_time.slice(0, 5) : "--:--") +
      " - " +
      (booking.end_time ? booking.end_time.slice(0, 5) : "--:--")
    );

    drawer.querySelector(".hex-dispatch-drawer__details").innerHTML = [
      ["Boat", resource ? resource.name : "-"],
      ["When", whenLabel],
      ["Status", titleCase(booking.status || "")],
      ["Customer Phone", booking.customer_phone || "-"],
      ["Customer E-mail", booking.customer_email || "-"],
      ["Route", booking.route_summary || "-"],
      ["Transfer From", booking.pickup_location || "-"],
      ["Transfer To", booking.dropoff_location || "-"],
      ["Luggage", booking.luggage_details || "-"],
      ["Passengers", booking.passengers || "0"],
      ["Price", booking.booking_price == null ? "-" : ("EUR " + Number(booking.booking_price).toFixed(2))],
      ["Advance", booking.advance_amount == null ? "-" : ("EUR " + Number(booking.advance_amount).toFixed(2))],
      ["Fuel Policy", booking.fuel_included ? "Included in price" : (isTransferService(booking.service_type) ? "Included" : "Not included")],
      ["Sales Channel", salesChannelLabel(booking.sales_channel)],
      ["Equipment", equipmentLabels(booking.extra_equipment).length ? equipmentLabels(booking.extra_equipment).join(", ") : "-"],
      ["Confirmation", booking.generate_confirmation ? "Enabled" : "Off"],
      ["Manager Notification", booking.generate_manager_notification ? "Enabled" : "Off"],
      ["Booking Notes", booking.notes || "-"],
      ["Internal Notes", booking.internal_notes || "-"],
    ].map(function (entry) {
      return "<div><dt>" + escapeHtml(entry[0]) + "</dt><dd>" + escapeHtml(entry[1]) + "</dd></div>";
    }).join("");

    state.currentConfirmationPreview = confirmationPreview && confirmationPreview.enabled ? confirmationPreview : null;
    state.currentManagerNotification = managerNotification && managerNotification.enabled ? managerNotification : null;
    var confirmationButton = $("[data-drawer-preview-confirmation]");
    var managerPreviewButton = $("[data-drawer-preview-manager-note]");
    var copyButton = $("[data-drawer-copy-manager-note]");
    var sendButton = $("[data-drawer-send-manager-note]");
    if (confirmationButton) {
      confirmationButton.hidden = !state.currentConfirmationPreview;
    }
    if (managerPreviewButton) {
      managerPreviewButton.hidden = !state.currentManagerNotification;
    }
    if (copyButton) {
      copyButton.hidden = !state.currentManagerNotification;
    }
    if (sendButton) {
      sendButton.hidden = !state.currentManagerNotification;
    }

    drawer.setAttribute("data-booking-id", String(booking.id));
    drawer.hidden = false;
    document.body.classList.add("hex-dispatch-drawer-open");
  }

  function closeDrawer() {
    var drawer = $("#hex-dispatch-drawer");
    if (!drawer) {
      return;
    }

    drawer.hidden = true;
    drawer.removeAttribute("data-booking-id");
    state.currentConfirmationPreview = null;
    state.currentManagerNotification = null;
    document.body.classList.remove("hex-dispatch-drawer-open");
  }

  async function openBookingDetails(bookingId) {
    try {
      var payload = await request("bookings/" + bookingId, { method: "GET" });
      openDrawerWithBooking(payload.booking, payload.manager_notification, payload.confirmation);
    } catch (error) {
      setToast(error.message || "Could not load booking details.", "error");
    }
  }

  async function openBookingInForm(bookingId) {
    try {
      var payload = await request("bookings/" + bookingId, { method: "GET" });
      populateFormFromBooking(payload.booking);
      setFormStatus(payload.confirmation, payload.manager_notification);
      showScreen("new");
      closeDrawer();
    } catch (error) {
      setToast(error.message || "Could not open booking.", "error");
    }
  }

  async function refreshLists() {
    await Promise.all([loadTodayEvents(), loadMyEvents()]);
  }

  async function saveForm(event) {
    event.preventDefault();

    try {
      setFormMessage("Saving booking...");
      var payload = buildPayloadFromForm();
      var bookingId = state.currentBookingId;
      var path = bookingId ? "bookings/" + bookingId : "bookings";
      var method = bookingId ? "PUT" : "POST";

      var response = await request(path, {
        method: method,
        body: JSON.stringify(payload),
      });

      await refreshLists();
      resetForm();
      setFormStatus(response.confirmation, response.manager_notification);
      showScreen("new");
      var messages = [bookingId ? "Booking updated." : "Booking created."];
      if (response.confirmation && response.confirmation.message) {
        messages.push(response.confirmation.message);
      }
      if (response.manager_notification && response.manager_notification.enabled) {
        messages.push("Manager notification is ready.");
      }
      setToast(
        messages.join(" "),
        response.confirmation && response.confirmation.enabled && !response.confirmation.sent ? "error" : "success"
      );
    } catch (error) {
      setFormMessage(error.message || "Could not save booking.", "error");
      setFormStatus(null, null);
    }
  }

  async function cancelCurrentBooking() {
    if (!state.currentBookingId) {
      return;
    }

    if (!window.confirm("Cancel this booking?")) {
      return;
    }

    try {
      await request("bookings/" + state.currentBookingId, { method: "DELETE" });
      await refreshLists();
      resetForm();
      showScreen("today");
      setToast("Booking cancelled.", "success");
    } catch (error) {
      setFormMessage(error.message || "Could not cancel booking.", "error");
      setFormStatus(null, null);
    }
  }

  function bindNavigation() {
    document.querySelectorAll("[data-screen-target]").forEach(function (button) {
      button.addEventListener("click", function () {
        showScreen(button.getAttribute("data-screen-target"));
      });
    });
  }

  function bindTodayFilters() {
    var container = $("#hex-dispatch-today-filters");
    if (!container) {
      return;
    }

    container.addEventListener("click", function (event) {
      var chip = event.target.closest(".hex-dispatch-chip");
      if (!chip) {
        return;
      }

      setActiveChip(chip.getAttribute("data-filter"), chip.getAttribute("data-value"));
    });
  }

  function bindLists() {
    document.addEventListener("click", function (event) {
      var bookingCard = event.target.closest("[data-booking-id]");
      if (bookingCard && !event.target.closest("[data-drawer-edit]")) {
        openBookingDetails(bookingCard.getAttribute("data-booking-id"));
        return;
      }

      var openForm = event.target.closest("[data-dispatch-open-form]");
      if (openForm) {
        resetForm();
        showScreen("new");
        return;
      }

      var boatCard = event.target.closest("[data-resource-id]");
      if (boatCard) {
        resetForm({
          resource_id: boatCard.getAttribute("data-resource-id"),
          booking_date: todayValue(),
        });
        showScreen("new");
      }
    });
  }

  function bindDrawer() {
    document.querySelectorAll("[data-drawer-close]").forEach(function (button) {
      button.addEventListener("click", closeDrawer);
    });

    var editButton = $("[data-drawer-edit]");
    if (editButton) {
      editButton.addEventListener("click", function () {
        var drawer = $("#hex-dispatch-drawer");
        var bookingId = drawer ? drawer.getAttribute("data-booking-id") : "";
        if (bookingId) {
          openBookingInForm(bookingId);
        }
      });
    }

    var copyManagerButton = $("[data-drawer-copy-manager-note]");
    if (copyManagerButton) {
      copyManagerButton.addEventListener("click", async function () {
        if (!state.currentManagerNotification || !state.currentManagerNotification.text) {
          return;
        }

        var copied = await copyText(state.currentManagerNotification.text);
        if (copied) {
          setToast("Manager note copied to clipboard.", "success");
        } else {
          setToast("Could not copy manager note.", "error");
        }
      });
    }

    var previewConfirmationButton = $("[data-drawer-preview-confirmation]");
    if (previewConfirmationButton) {
      previewConfirmationButton.addEventListener("click", function () {
        if (!state.currentConfirmationPreview) {
          return;
        }

        openPreview(
          state.currentConfirmationPreview.subject || "Booking Confirmation",
          state.currentConfirmationPreview.recipient ? "To: " + state.currentConfirmationPreview.recipient : "Booking Confirmation",
          state.currentConfirmationPreview.text || ""
        );
      });
    }

    var previewManagerButton = $("[data-drawer-preview-manager-note]");
    if (previewManagerButton) {
      previewManagerButton.addEventListener("click", function () {
        if (!state.currentManagerNotification) {
          return;
        }

        openPreview(
          "Manager Notification",
          state.currentManagerNotification.scheduled_time ? "Daily target: " + state.currentManagerNotification.scheduled_time : "Manager Note",
          state.currentManagerNotification.text || ""
        );
      });
    }

    var sendManagerButton = $("[data-drawer-send-manager-note]");
    if (sendManagerButton) {
      sendManagerButton.addEventListener("click", async function () {
        if (!state.currentManagerNotification || !state.currentManagerNotification.text) {
          return;
        }

        if (!state.currentManagerNotification.whatsapp_url) {
          setToast("Manager WhatsApp number is not configured yet.", "error");
          return;
        }

        await copyText(state.currentManagerNotification.text);
        window.open(state.currentManagerNotification.whatsapp_url, "_blank", "noopener");
        setToast("Manager notification opened in WhatsApp.", "success");
      });
    }

    document.querySelectorAll("[data-preview-close]").forEach(function (button) {
      button.addEventListener("click", closePreview);
    });

    var previewCopyButton = $("[data-preview-copy]");
    if (previewCopyButton) {
      previewCopyButton.addEventListener("click", async function () {
        var text = $("#hex-dispatch-preview-text");
        var copied = await copyText(text ? text.textContent : "");
        setToast(copied ? "Preview text copied." : "Could not copy preview text.", copied ? "success" : "error");
      });
    }

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        closeDrawer();
        closePreview();
      }
    });
  }

  function bindForm() {
    var form = dispatchForm();
    if (!form) {
      return;
    }

    form.addEventListener("submit", saveForm);
    form.elements.service_type.addEventListener("change", function () {
      syncServiceModeUI();
      syncSkipperRules();
    });
    form.elements.resource_id.addEventListener("change", syncSkipperRules);
    form.elements.is_all_day.addEventListener("change", toggleTimeFields);
    form.elements.pickup_location.addEventListener("change", syncTransferCustomFields);
    form.elements.dropoff_location.addEventListener("change", syncTransferCustomFields);

    document.querySelectorAll("[data-hex-map-check]").forEach(function (button) {
      button.addEventListener("click", function () {
        openMapTarget(button.getAttribute("data-hex-map-check"));
      });
    });

    $("[data-dispatch-reset-form]").addEventListener("click", function () {
      resetForm();
    });

    $("[data-dispatch-cancel-booking]").addEventListener("click", cancelCurrentBooking);

    var sendConfirmationButton = $("[data-status-send-confirmation]");
    if (sendConfirmationButton) {
      sendConfirmationButton.addEventListener("click", async function () {
        if (!state.currentBookingId) {
          setToast("Save the booking first before sending confirmation.", "error");
          return;
        }

        try {
          var response = await request("bookings/" + state.currentBookingId + "/send-confirmation", {
            method: "POST",
            body: JSON.stringify({}),
          });
          setFormStatus(response.confirmation, state.currentManagerNotification);
          setToast(response.confirmation && response.confirmation.message ? response.confirmation.message : "Confirmation sent.", response.confirmation && response.confirmation.sent ? "success" : "error");
        } catch (error) {
          setToast(error.message || "Could not send confirmation.", "error");
        }
      });
    }
  }

  async function boot() {
    var form = dispatchForm();
    if (!form || typeof hexBookingsConfig === "undefined") {
      return;
    }

    try {
      var bootstrap = await Promise.all([
        request("system", { method: "GET" }),
        request("resources", { method: "GET" }),
      ]);

      state.currentUser = bootstrap[0].current_user || {};
      state.canManage = !!(bootstrap[0].permissions && bootstrap[0].permissions.can_manage);
      state.bookers = Array.isArray(bootstrap[0].bookers) ? bootstrap[0].bookers : [];
      state.salesChannels = Array.isArray(bootstrap[0].sales_channels) ? bootstrap[0].sales_channels : [];
      state.equipmentOptions = Array.isArray(bootstrap[0].equipment_options) ? bootstrap[0].equipment_options : [];
      state.transferLocations = Array.isArray(bootstrap[0].transfer_locations) ? bootstrap[0].transfer_locations : [];
      state.notificationSettings = bootstrap[0].notification_settings || {};
      state.resources = bootstrap[1].resources || [];

      if (!state.canManage) {
        state.todayFilters.scope = "mine";
      }

      populateUserCard();
      populateTodayLabel();
      populateFormSelects();
      resetForm();
      bindNavigation();
      bindTodayFilters();
      bindLists();
      bindDrawer();
      bindForm();

      setActiveChip("scope", state.todayFilters.scope);
      await refreshLists();
    } catch (error) {
      setToast(error.message || "Could not load dispatch app.", "error");
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
