$(document).ready(function () {
    $("#more_dates")
        .parent()
        .children("label")
        .toggleWithLegend($("#more_dates").parent().children().not("label"), {
            user_pref: "dcx_post_more_dates",
            legend_click: true,
        });

    const creadtTodayHelper = (e) => {
        e.preventDefault();
        const field = e.currentTarget.previousElementSibling;
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        field.value = now.toISOString().slice(0, 16);
    };

    const upddtTodayHelper = (e) => {
        e.preventDefault();
        const field = e.currentTarget.previousElementSibling;
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        field.value = now.toISOString().slice(0, 16);
    };

    const creadtTodayButtonTemplate = new DOMParser().parseFromString(
        `<button type="button" class="dt-today" title="${dotclear.msg.set_today}"><span class="sr-only">${dotclear.msg.set_today}</span></button>`,
        "text/html"
    ).body.firstChild;

    const creadtField = document.querySelector("#post_creadt");
    const creadtbutton = creadtTodayButtonTemplate.cloneNode(true);
    creadtField.after(creadtbutton);
    creadtField.classList.add("today_helper");
    creadtbutton.addEventListener("click", creadtTodayHelper);

    const upddtTodayButtonTemplate = new DOMParser().parseFromString(
        `<button type="button" class="dt-today" title="${dotclear.msg.set_today}"><span class="sr-only">${dotclear.msg.set_today}</span></button>`,
        "text/html"
    ).body.firstChild;

    const upddtField = document.querySelector("#post_upddt");
    const upddtbutton = upddtTodayButtonTemplate.cloneNode(true);
    upddtField.after(upddtbutton);
    upddtField.classList.add("today_helper");
    upddtbutton.addEventListener("click", upddtTodayHelper);
});
