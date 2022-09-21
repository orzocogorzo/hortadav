function HortadavSelector(data) {
  let _options = [];
  let _events = [];
  let _data = [];

  function render() {
    $el = document.createElement("select");
    $el.multiple = true;
    return $el;
  }

  function _Selector() {
    this.$el = render();

    Object.defineProperties(this, {
      data: {
        get: function () {
          return data.map((e, i) => ({ ...e }));
        },
      },
      options: {
        get: function () {
          return _options.map(o => o);
        },
      },
      events: {
        get: function () {
          return _events.map(e => ({ ...e }));
        },
      },
    });
  }

  _Selector.prototype.setData = function (data) {
    _data = data;
    _options = Object.entries(
      data.reduce(function (acum, ev) {
        acum[ev.plant] = true;
        return acum;
      }, {})
    ).map(function (e) {
      return e[0];
    });

    const self = this;
    _options.forEach(function (plant, i) {
      const option = document.createElement("option");
      option.value = i;
      option.innerText = plant;
      self.$el.appendChild(option);
    });

    this.$el.value = null;

    return this;
  };

  _Selector.prototype.onChange = function (fn) {
    const self = this;
    const parent = this.$el.parentNode;
    this.$el = render();
    this.setData(_data);
    this.$el.addEventListener("change", function (ev) {
      _events = Array.apply(null, ev.target.children).reduce(function (acum, opt) {
        if (opt.selected) {
          return acum.concat(
            _data.filter(function (d) {
              return d.plant === _options[opt.value];
            })
          );
        }
        return acum;
      }, []);
      fn(self.events);
    });
    if (parent) this.renderOn(parent);

    return this;
  };

  _Selector.prototype.renderOn = function ($el) {
    $el.innerHTML = "";
    $el.appendChild(this.$el);

    return this;
  };

  _Selector.prototype.clear = function () {
    this.$el.value = null;
    this.$el.dispatchEvent(new Event("change"));
  };

  _Selector.prototype.export = function () {
    console.log(this.events);
  };

  return new _Selector();
}

function HortadavCalendar(settings) {
  let events = [];
  let selection = null;

  const html = `<div class="hortadav__toolbar">
    <div class="hortadav__selector"></div>
    <div class="hortadav__buttons">
      <button class="hortadav__clear">Neteja</button>
      <button class="hortadav__download">Descarrega</button>
    </div>
  </div>
  <div class="hortadav__calendar"></div>
  `;

  function _HortadavCalendar() {
    const self = this;

    const $el = document.getElementById(settings.el);
    $el.innerHTML = html;

    const selector = new HortadavSelector()
      .setData(settings.data)
      .onChange(function (events) {
        calendar.getEvents().forEach(function (ev) {
          ev.remove();
        });
        events.forEach(function (ev) {
          calendar.addEvent({
            title: ev.title,
            start: self.parseDate(ev.dates.start),
            end: self.parseDate(ev.dates.end),
            extendedProps: {
              description: ev.description,
            },
          });
        });
        calendar.render();
      })
      .renderOn($el.getElementsByClassName("hortadav__selector")[0]);

    const calendar = new FullCalendar.Calendar(
      $el.getElementsByClassName("hortadav__calendar")[0],
      {
        initialView: "dayGridMonth",
        eventMouseEnter: info => {
          tooltip.innerHTML = info.event.extendedProps.description;
          tooltip.style.display = "block";
        },
        eventMouseLeave: () => {
          tooltip.style.display = "none";
        },
        events: selector.events.map(function (ev) {
          return {
            title: ev.title,
            start: self.parseDate(ev.dates.start),
            end: self.parseDate(ev.dates.end),
            extendedProps: {
              description: ev.description,
            },
          };
        }),
      }
    );
    calendar.render();

    const tooltip = document.createElement("div");
    tooltip.classList.add("hortadav__tooltip");
    tooltip.style.position = "absolute";
    tooltip.style.zIndex = 1000;
    tooltip.style.backgroundColor = "#ffffff";
    tooltip.style.padding = "2rem";
    tooltip.style.display = "none";
    tooltip.style.width = "300px";
    tooltip.style.fontSize = "12px";
    tooltip.style.textAlign = "center";
    tooltip.style.borderRadius = "5px";
    $el.appendChild(tooltip);

    const clearBtn = $el.getElementsByClassName("hortadav__clear")[0];
    clearBtn.addEventListener("click", () => selector.clear());

    const downloadBtn = $el.getElementsByClassName("hortadav__download")[0];
    downloadBtn.addEventListener("click", () => this.exportICal());

    Object.defineProperty(this, "events", {
      get: () => selector.events,
    });
  }

  _HortadavCalendar.prototype.parseDate = function (str) {
    if (!str) return null;
    var year = str.slice(0, 4);
    var month = str.slice(4, 6);
    var day = str.slice(6, 8);
    return new Date(year, month - 1, day);
  };

  _HortadavCalendar.prototype.eventToICal = function (ev) {
    return ev.title + "\n";
  };

  _HortadavCalendar.prototype.exportICal = function () {
    const ical = this.events.map(ev => this.eventToICal(ev)).join("\n");
    const anchor = document.createElement("a");
    anchor.href = "data:text/plain;charset=utf-8, " + encodeURIComponent(ical);
    anchor.download = "calendari_horta.ics";
    document.body.appendChild(anchor);
    anchor.click();
    document.body.removeChild(anchor);
  };

  return new _HortadavCalendar();
}
