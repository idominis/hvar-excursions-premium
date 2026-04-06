(function () {
  var state = {
    calendar: null,
    resources: [],
    currentBookingId: null,
    currentUserId: 0,
    canManage: false,
    bookers: [],
    filterValues: {
      category: "",
      resource_id: "",
      booker_user_id: "",
      service_type: "",
      status: "",
      only_mine: false,
    },
    persistTimer: null,
  };

  function $(selector) {
    return document.querySelector(selector);
  }

  function bookingForm() {
    return $("#hex-bookings-form");
  }

  function filtersForm() {
    return $("#hex-bookings-filters");
  }

  function popoverNode() {
    return $("#hex-bookings-event-popover");
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function titleCase(value) {
    return String(value || "")
      .replace(/-/g, " ")
      .replace(/\b\w/g, function (char) {
        return char.toUpperCase();
      });
  }

  function toTimeValue(date) {
    if (!(date instanceof Date)) {
      date = new Date(date);
    }

    return String(date.getHours()).padStart(2, "0") + ":" + String(date.getMinutes()).padStart(2, "0");
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

  function setMessage(message, type) {
    var node = $("#hex-bookings-form-message");
    if (!node) {
      return;
    }

    node.textContent = message || "";
    node.classList.remove("is-error", "is-success");
    if (type) {
      node.classList.add(type === "error" ? "is-error" : "is-success");
    }
  }

  function hideEventPopover() {
    var popover = popoverNode();
    if (!popover) {
      return;
    }

    popover.hidden = true;
    popover.removeAttribute("data-booking-id");
  }

  function schedulePersistFilters() {
    if (state.persistTimer) {
      window.clearTimeout(state.persistTimer);
    }

    state.persistTimer = window.setTimeout(function () {
      persistFilters().catch(function () {});
    }, 250);
  }

  async function persistFilters() {
    await request("preferences", {
      method: "POST",
      body: JSON.stringify({
        filters: state.filterValues,
      }),
    });
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

  function getResourceById(resourceId) {
    var id = Number(resourceId);
    return state.resources.find(function (resource) {
      return Number(resource.id) === id;
    }) || null;
  }

  function getFilteredResources() {
    return state.resources.filter(function (resource) {
      if (state.filterValues.category && resource.category !== state.filterValues.category) {
        return false;
      }

      if (state.filterValues.resource_id && String(resource.id) !== String(state.filterValues.resource_id)) {
        return false;
      }

      return true;
    });
  }

  function syncSkipperRules() {
    var form = bookingForm();
    var serviceSelect = form && form.elements.service_type;
    var skipperSelect = form && form.elements.skipper_mode;
    var resourceSelect = form && form.elements.resource_id;
    if (!serviceSelect || !skipperSelect || !resourceSelect) {
      return;
    }

    var resource = getResourceById(resourceSelect.value);
    var serviceType = serviceSelect.value;
    var forceSkipper = serviceType === "transfer" || serviceType === "taxi";
    var bareboatAllowed = !resource || Number(resource.supports_bareboat) === 1;

    skipperSelect.querySelectorAll("option").forEach(function (option) {
      option.disabled = false;
    });

    if (forceSkipper || !bareboatAllowed) {
      skipperSelect.value = "with_skipper";
      var withoutSkipper = skipperSelect.querySelector('option[value="without_skipper"]');
      if (withoutSkipper) {
        withoutSkipper.disabled = true;
      }
    }
  }

  function toggleTimeFields() {
    var form = bookingForm();
    var checked = !!(form && form.elements.is_all_day && form.elements.is_all_day.checked);
    ["start_time", "end_time"].forEach(function (fieldName) {
      var field = form && form.elements[fieldName];
      if (field) {
        field.disabled = checked;
      }
    });
  }

  function resourceOptionsMarkup(resources, includeAllLabel) {
    var options = [];

    if (includeAllLabel) {
      options.push('<option value="">' + escapeHtml(includeAllLabel) + "</option>");
    }

    resources.forEach(function (resource) {
      options.push(
        '<option value="' + escapeHtml(resource.id) + '">' +
        escapeHtml(resource.name + " (" + titleCase(resource.category) + ")") +
        "</option>"
      );
    });

    return options.join("");
  }

  function categoryOptionsMarkup(resources) {
    var categories = [];
    resources.forEach(function (resource) {
      if (categories.indexOf(resource.category) === -1) {
        categories.push(resource.category);
      }
    });

    return ['<option value="">All categories</option>'].concat(
      categories.map(function (category) {
        return '<option value="' + escapeHtml(category) + '">' + escapeHtml(titleCase(category)) + "</option>";
      })
    ).join("");
  }

  function resetForm(prefill) {
    var form = bookingForm();
    if (!form) {
      return;
    }

    hideEventPopover();
    state.currentBookingId = null;
    form.reset();
    form.booking_id.value = "";
    $('[data-hex-delete-booking]').hidden = true;

    if (state.resources.length) {
      form.elements.resource_id.value = String(state.resources[0].id);
    }

    form.elements.status.value = "draft";
    form.elements.service_type.value = "rental";
    form.elements.skipper_mode.value = "with_skipper";
    if (form.elements.book_as_user_id) {
      form.elements.book_as_user_id.value = state.canManage ? String(state.currentUserId || "") : "";
    }
    form.elements.booking_date.value = toDateValue(new Date());
    form.elements.start_time.value = "09:00";
    form.elements.end_time.value = "17:00";

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
    syncSkipperRules();
    setMessage("");
  }

  function populateFormFromBooking(booking) {
    var form = bookingForm();
    if (!form) {
      return;
    }

    state.currentBookingId = Number(booking.id);
    form.booking_id.value = String(booking.id);
    form.elements.resource_id.value = String(booking.resource_id);
    form.elements.booking_date.value = booking.booking_date;
    form.elements.status.value = booking.status;
    form.elements.service_type.value = booking.service_type;
    form.elements.skipper_mode.value = booking.skipper_mode;
    if (form.elements.book_as_user_id) {
      form.elements.book_as_user_id.value = booking.booker_user_id ? String(booking.booker_user_id) : String(state.currentUserId || "");
    }
    form.elements.customer_name.value = booking.customer_name || "";
    form.elements.customer_phone.value = booking.customer_phone || "";
    form.elements.route_summary.value = booking.route_summary || "";
    form.elements.passengers.value = booking.passengers || "";
    form.elements.internal_notes.value = booking.internal_notes || "";
    form.elements.is_all_day.checked = Number(booking.is_all_day) === 1;
    form.elements.start_time.value = booking.start_time ? booking.start_time.slice(0, 5) : "";
    form.elements.end_time.value = booking.end_time ? booking.end_time.slice(0, 5) : "";
    $('[data-hex-delete-booking]').hidden = false;

    toggleTimeFields();
    syncSkipperRules();
    setMessage("Loaded booking #" + booking.id, "success");
  }

  function buildPayloadFromForm() {
    var form = bookingForm();
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
      route_summary: form.elements.route_summary.value,
      passengers: form.elements.passengers.value ? Number(form.elements.passengers.value) : 0,
      internal_notes: form.elements.internal_notes.value,
      source: "wp_internal",
    };

    if (state.canManage && form.elements.book_as_user_id && form.elements.book_as_user_id.value) {
      payload.book_as_user_id = Number(form.elements.book_as_user_id.value);
    }

    return payload;
  }

  function mapResources(resources) {
    return resources.map(function (resource) {
      return {
        id: String(resource.id),
        title: resource.name,
        category: titleCase(resource.category),
        capacity: resource.capacity ? resource.capacity + " pax" : "-",
        sortOrder: Number(resource.sort_order || 0),
        eventColor: resource.color || "#1f6bff",
      };
    });
  }

  function getDateRangeQuery() {
    var view = state.calendar.view;
    var params = new URLSearchParams();
    params.set("date_from", toDateValue(view.activeStart));
    params.set("date_to", toDateValue(new Date(view.activeEnd.getTime() - 86400000)));

    if (state.filterValues.resource_id) {
      params.set("resource_id", state.filterValues.resource_id);
    }
    if (state.filterValues.booker_user_id && state.canManage && !state.filterValues.only_mine) {
      params.set("booker_user_id", state.filterValues.booker_user_id);
    }
    if (state.filterValues.service_type) {
      params.set("service_type", state.filterValues.service_type);
    }
    if (state.filterValues.status) {
      params.set("status", state.filterValues.status);
    }
    if (state.filterValues.only_mine) {
      params.set("only_mine", "1");
    }

    return params.toString();
  }

  function snapshotCalendarState() {
    if (!state.calendar) {
      return {
        viewType: "resourceTimelineWeek",
        date: new Date(),
      };
    }

    return {
      viewType: state.calendar.view.type,
      date: state.calendar.getDate(),
    };
  }

  async function refreshEvents() {
    if (!state.calendar) {
      return;
    }

    hideEventPopover();
    var payload = await request("bookings?" + getDateRangeQuery(), { method: "GET" });
    state.calendar.removeAllEvents();
    state.calendar.addEventSource(payload.events || []);
  }

  async function loadBooking(bookingId) {
    var payload = await request("bookings/" + bookingId, { method: "GET" });
    populateFormFromBooking(payload.booking);
  }

  function formatEventDateRange(event) {
    var start = event.start;
    var end = event.end || event.start;
    var dateLabel = start.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    });

    if (event.allDay) {
      return dateLabel + " - All day";
    }

    var startLabel = start.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    });
    var endLabel = end.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    });

    return dateLabel + " - " + startLabel + " to " + endLabel;
  }

  function showEventPopover(info) {
    var popover = popoverNode();
    var panel = info.el && info.el.closest(".hex-bookings-app__panel");
    if (!popover || !panel) {
      return;
    }

    var event = info.event;
    var props = event.extendedProps || {};
    var resource = event.getResources()[0];
    var titleNode = popover.querySelector(".hex-bookings-popover__title");
    var metaNode = popover.querySelector(".hex-bookings-popover__meta");
    var detailsNode = popover.querySelector(".hex-bookings-popover__details");

    titleNode.textContent = event.title || "Booking";
    metaNode.innerHTML =
      '<span class="hex-bookings-popover__pill">' + escapeHtml(props.badge || "Booking") + "</span>" +
      '<span class="hex-bookings-popover__pill">' + escapeHtml(props.booker_initials || "--") + "</span>";

    detailsNode.innerHTML = [
      ["Boat", resource ? resource.title : "-"],
      ["When", formatEventDateRange(event)],
      ["Booker", props.booker_initials || "--"],
      ["Status", titleCase(props.status || "")],
      ["Service", titleCase(props.service_type || "")],
      ["Skipper", titleCase((props.skipper_mode || "").replace(/_/g, " "))],
      ["Route", props.route_summary || "-"],
    ].map(function (entry) {
      return "<div><dt>" + escapeHtml(entry[0]) + "</dt><dd>" + escapeHtml(entry[1]) + "</dd></div>";
    }).join("");

    popover.setAttribute("data-booking-id", String(event.id));
    popover.hidden = false;

    var panelRect = panel.getBoundingClientRect();
    var popoverWidth = popover.offsetWidth || 320;
    var popoverHeight = popover.offsetHeight || 240;
    var left = info.jsEvent.clientX - panelRect.left + 16;
    var top = info.jsEvent.clientY - panelRect.top + 16;
    var maxLeft = panel.clientWidth - popoverWidth - 16;
    var maxTop = panel.clientHeight - popoverHeight - 16;

    if (left > maxLeft) {
      left = Math.max(16, maxLeft);
    }

    if (top > maxTop) {
      top = Math.max(16, maxTop);
    }

    popover.style.left = left + "px";
    popover.style.top = top + "px";
  }

  async function saveBookingFromForm(event) {
    event.preventDefault();

    try {
      setMessage("Saving booking...");
      var payload = buildPayloadFromForm();
      var bookingId = state.currentBookingId;
      var path = bookingId ? "bookings/" + bookingId : "bookings";
      var method = bookingId ? "PUT" : "POST";

      await request(path, {
        method: method,
        body: JSON.stringify(payload),
      });

      await refreshEvents();
      if (bookingId) {
        await loadBooking(bookingId);
        setMessage("Booking updated.", "success");
      } else {
        resetForm();
        setMessage("Booking created.", "success");
      }
    } catch (error) {
      setMessage(error.message || "Could not save booking.", "error");
    }
  }

  async function deleteBooking() {
    if (!state.currentBookingId) {
      return;
    }

    if (!window.confirm("Cancel this booking?")) {
      return;
    }

    try {
      await request("bookings/" + state.currentBookingId, {
        method: "DELETE",
      });
      await refreshEvents();
      resetForm();
      setMessage("Booking cancelled.", "success");
    } catch (error) {
      setMessage(error.message || "Could not cancel booking.", "error");
    }
  }

  async function saveCalendarMutation(calendarEvent, revert) {
    try {
      await request("bookings/" + calendarEvent.id, {
        method: "PUT",
        body: JSON.stringify({
          resource_id: Number(calendarEvent.getResources()[0].id),
          booking_date: toDateValue(calendarEvent.start),
          start_time: calendarEvent.allDay ? "" : toTimeValue(calendarEvent.start),
          end_time: calendarEvent.allDay ? "" : toTimeValue(calendarEvent.end || calendarEvent.start),
          is_all_day: calendarEvent.allDay,
          status: calendarEvent.extendedProps.status,
          service_type: calendarEvent.extendedProps.service_type,
          skipper_mode: calendarEvent.extendedProps.skipper_mode,
          customer_name: calendarEvent.extendedProps.customer_name || "",
          route_summary: calendarEvent.extendedProps.route_summary || "",
          source: "wp_internal",
        }),
      });

      await refreshEvents();
    } catch (error) {
      revert();
      setMessage(error.message || "Could not move booking.", "error");
    }
  }

  function renderEventContent(arg) {
    var badge = arg.event.extendedProps.badge || "";
    var initials = arg.event.extendedProps.booker_initials || "";
    return {
      html:
        '<div class="hex-event">' +
        '<div class="hex-event__title">' + escapeHtml(arg.event.title || "Booking") + "</div>" +
        '<div class="hex-event__meta">' +
        '<span class="hex-event__badge">' + escapeHtml(badge) + "</span>" +
        '<span class="hex-event__initials">' + escapeHtml(initials) + "</span>" +
        "</div>" +
        "</div>",
    };
  }

  function collectFilterValues() {
    var form = filtersForm();
    if (!form) {
      return;
    }

    state.filterValues.category = form.elements.category.value;
    state.filterValues.resource_id = form.elements.resource_id.value;
    state.filterValues.booker_user_id = form.elements.booker_user_id.value;
    state.filterValues.service_type = form.elements.service_type.value;
    state.filterValues.status = form.elements.status.value;
    state.filterValues.only_mine = !!form.elements.only_mine.checked;

    if (state.filterValues.only_mine) {
      state.filterValues.booker_user_id = "";
      form.elements.booker_user_id.value = "";
      if (state.canManage) {
        form.elements.booker_user_id.disabled = true;
      }
    } else if (state.canManage) {
      form.elements.booker_user_id.disabled = false;
    }
  }

  function populateFilterOptions() {
    var form = bookingForm();
    var filters = filtersForm();

    form.elements.resource_id.innerHTML = resourceOptionsMarkup(state.resources);
    filters.elements.resource_id.innerHTML = resourceOptionsMarkup(state.resources, "All boats");
    filters.elements.category.innerHTML = categoryOptionsMarkup(state.resources);

    if (state.canManage) {
      filters.elements.booker_user_id.innerHTML = ['<option value="">All bookers</option>'].concat(
        state.bookers.map(function (booker) {
          var label = booker.display_name + (booker.email ? " (" + booker.email + ")" : "");
          return '<option value="' + escapeHtml(booker.id) + '">' + escapeHtml(label) + "</option>";
        })
      ).join("");
      filters.elements.booker_user_id.disabled = false;
      filters.elements.booker_user_id.closest("label").hidden = false;
    } else {
      filters.elements.booker_user_id.innerHTML = '<option value="">All bookers</option>';
      filters.elements.booker_user_id.disabled = true;
      filters.elements.booker_user_id.closest("label").hidden = true;
    }
  }

  function populateEditorBookerOptions() {
    var form = bookingForm();
    if (!form || !form.elements.book_as_user_id) {
      return;
    }

    var wrapper = form.elements.book_as_user_id.closest(".hex-bookings-form__book-as");

    if (!state.canManage) {
      form.elements.book_as_user_id.innerHTML = '<option value=""></option>';
      if (wrapper) {
        wrapper.hidden = true;
      }
      return;
    }

    form.elements.book_as_user_id.innerHTML = state.bookers.map(function (booker) {
      var label = (booker.initials ? "[" + booker.initials + "] " : "") + booker.display_name + (booker.email ? " - " + booker.email : "");
      return '<option value="' + escapeHtml(booker.id) + '">' + escapeHtml(label) + "</option>";
    }).join("");

    form.elements.book_as_user_id.value = String(state.currentUserId || "");

    if (wrapper) {
      wrapper.hidden = false;
    }
  }

  function populateCurrentUserCard(user) {
    var card = $("#hex-bookings-current-user");
    if (!card || !user) {
      return;
    }

    var initialsNode = card.querySelector(".hex-bookings-app__user-initials");
    var nameNode = card.querySelector(".hex-bookings-app__user-name");
    var emailNode = card.querySelector(".hex-bookings-app__user-email");

    if (initialsNode) {
      initialsNode.textContent = user.initials || "--";
    }

    if (nameNode) {
      nameNode.textContent = user.display_name || user.initials || "Unknown user";
    }

    if (emailNode) {
      emailNode.textContent = user.email || "";
    }
  }

  function applySavedFilters(savedFilters) {
    var form = filtersForm();
    if (!form || !savedFilters) {
      return;
    }

    if (savedFilters.category) {
      form.elements.category.value = String(savedFilters.category);
    }
    if (savedFilters.resource_id) {
      form.elements.resource_id.value = String(savedFilters.resource_id);
    }
    if (savedFilters.booker_user_id && state.canManage) {
      form.elements.booker_user_id.value = String(savedFilters.booker_user_id);
    }
    if (savedFilters.service_type) {
      form.elements.service_type.value = String(savedFilters.service_type);
    }
    if (savedFilters.status) {
      form.elements.status.value = String(savedFilters.status);
    }
    form.elements.only_mine.checked = !!savedFilters.only_mine;
  }

  function buildCalendarOptions(calendarState) {
    return {
      schedulerLicenseKey: hexBookingsConfig.licenseKey,
      initialView: calendarState.viewType || "resourceTimelineWeek",
      initialDate: calendarState.date || new Date(),
      views: {
        resourceTimelineDay: {
          type: "resourceTimeline",
          duration: { days: 1 },
          slotDuration: { hours: 1 },
          slotLabelInterval: { hours: 1 },
          slotLabelFormat: [
            { weekday: "short", day: "numeric", month: "short" },
            { hour: "2-digit", minute: "2-digit", hour12: false },
          ],
        },
        resourceTimelineWeek: {
          type: "resourceTimeline",
          duration: { days: 7 },
          slotDuration: { days: 1 },
          slotLabelInterval: { days: 1 },
          slotLabelFormat: { weekday: "short", day: "numeric" },
        },
        resourceTimelineMonth: {
          type: "resourceTimeline",
          duration: { months: 1 },
          slotDuration: { days: 1 },
          slotLabelInterval: { days: 1 },
          slotLabelFormat: { weekday: "short", day: "numeric" },
        },
      },
      headerToolbar: {
        left: "prev,next today newBookingButton",
        center: "title",
        right: "resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth",
      },
      customButtons: {
        newBookingButton: {
          text: "New Booking",
          click: function () {
            resetForm();
          },
        },
      },
      timeZone: hexBookingsConfig.timezone || "local",
      nowIndicator: true,
      editable: true,
      selectable: true,
      selectMirror: true,
      height: "auto",
      resourceGroupField: "category",
      resourceAreaWidth: "30%",
      resourceAreaColumns: [
        { field: "title", headerContent: "Boat" },
        { field: "capacity", headerContent: "Capacity" },
      ],
      resourceOrder: "category,sortOrder,title",
      slotMinWidth: 70,
      eventMinWidth: 36,
      scrollTime: "08:00:00",
      resources: mapResources(getFilteredResources()),
      eventContent: renderEventContent,
      datesSet: function () {
        refreshEvents().catch(function (error) {
          setMessage(error.message || "Could not load bookings.", "error");
        });
      },
      select: function (info) {
        hideEventPopover();
        resetForm({
          resource_id: info.resource ? String(info.resource.id) : "",
          booking_date: toDateValue(info.start),
          start_time: info.allDay ? "" : toTimeValue(info.start),
          end_time: info.allDay ? "" : toTimeValue(info.end),
          is_all_day: info.allDay,
        });
        state.calendar.unselect();
        setMessage("New booking prefilled from selected timeline slot.", "success");
      },
      eventClick: function (info) {
        info.jsEvent.preventDefault();
        showEventPopover(info);
      },
      eventDrop: function (info) {
        saveCalendarMutation(info.event, info.revert);
      },
      eventResize: function (info) {
        saveCalendarMutation(info.event, info.revert);
      },
    };
  }

  async function renderCalendar(preserveState) {
    var root = $("#hex-bookings-calendar-root");
    var calendarState = preserveState || snapshotCalendarState();

    if (state.calendar) {
      state.calendar.destroy();
      state.calendar = null;
    }

    root.classList.add("is-loading");
    root.textContent = "Loading calendar...";

    state.calendar = new FullCalendar.Calendar(root, buildCalendarOptions(calendarState));
    root.classList.remove("is-loading");
    root.textContent = "";
    state.calendar.render();
    await refreshEvents();
  }

  function bindFilterEvents() {
    var form = filtersForm();
    if (!form) {
      return;
    }

    form.addEventListener("change", function (event) {
      collectFilterValues();
      schedulePersistFilters();
      var preserveState = snapshotCalendarState();

      if (event.target.name === "category" || event.target.name === "resource_id") {
        renderCalendar(preserveState).catch(function (error) {
          setMessage(error.message || "Could not apply filters.", "error");
        });
        return;
      }

      refreshEvents().catch(function (error) {
        setMessage(error.message || "Could not apply filters.", "error");
      });
    });

    $('[data-hex-clear-filters]').addEventListener("click", function () {
      form.reset();
      collectFilterValues();
      schedulePersistFilters();
      renderCalendar(snapshotCalendarState()).catch(function (error) {
        setMessage(error.message || "Could not clear filters.", "error");
      });
    });
  }

  function bindPopoverEvents() {
    var popover = popoverNode();
    if (!popover) {
      return;
    }

    var editButton = popover.querySelector("[data-hex-popover-edit]");
    var closeButton = popover.querySelector("[data-hex-popover-close]");

    if (editButton) {
      editButton.addEventListener("click", function () {
        var bookingId = popover.getAttribute("data-booking-id");
        if (!bookingId) {
          return;
        }

        loadBooking(bookingId).then(function () {
          hideEventPopover();
        }).catch(function (error) {
          setMessage(error.message || "Could not load booking.", "error");
        });
      });
    }

    if (closeButton) {
      closeButton.addEventListener("click", hideEventPopover);
    }

    document.addEventListener("click", function (event) {
      if (popover.hidden) {
        return;
      }

      if (event.target.closest("#hex-bookings-event-popover") || event.target.closest(".fc-event")) {
        return;
      }

      hideEventPopover();
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        hideEventPopover();
      }
    });
  }

  async function boot() {
    var root = $("#hex-bookings-calendar-root");
    var form = bookingForm();

    if (!root || !form || typeof FullCalendar === "undefined" || typeof hexBookingsConfig === "undefined") {
      return;
    }

    root.classList.add("is-loading");
    root.textContent = "Loading calendar...";

    try {
      var bootstrapPayload = await Promise.all([
        request("system", { method: "GET" }),
        request("resources", { method: "GET" }),
      ]);

      state.currentUserId = Number((bootstrapPayload[0].current_user && bootstrapPayload[0].current_user.id) || 0);
      state.canManage = !!(bootstrapPayload[0].permissions && bootstrapPayload[0].permissions.can_manage);
      state.bookers = Array.isArray(bootstrapPayload[0].bookers) ? bootstrapPayload[0].bookers : [];
      state.resources = bootstrapPayload[1].resources || [];

      populateCurrentUserCard(bootstrapPayload[0].current_user || {});
      populateFilterOptions();
      populateEditorBookerOptions();
      applySavedFilters(bootstrapPayload[0].saved_filters || {});
      collectFilterValues();
      resetForm();
      bindFilterEvents();
      bindPopoverEvents();
      await renderCalendar({
        viewType: "resourceTimelineWeek",
        date: new Date(),
      });

      setMessage("Timeline ready.", "success");
    } catch (error) {
      root.classList.remove("is-loading");
      root.innerHTML = "<p><strong>Calendar failed to load:</strong> " + escapeHtml(error.message) + "</p>";
      setMessage(error.message || "Could not initialize calendar.", "error");
    }

    form.addEventListener("submit", saveBookingFromForm);
    $('[data-hex-reset-form]').addEventListener("click", function () {
      resetForm();
    });
    $('[data-hex-delete-booking]').addEventListener("click", deleteBooking);
    form.elements.service_type.addEventListener("change", syncSkipperRules);
    form.elements.resource_id.addEventListener("change", syncSkipperRules);
    form.elements.is_all_day.addEventListener("change", toggleTimeFields);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
