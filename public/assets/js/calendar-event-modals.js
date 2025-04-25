class HearingModal {
    constructor(node, caseId, hearingId, description, date) {
        this.onDateChange = null;
        this.modalBody = node;
        this.caseId = caseId;
        this.hearingId = hearingId;
        this.description = description;
        this.date = date;
        this.url =`/cases/${caseId}/hearing/${hearingId}`
        this.init();
    }
    init() {
        this.render();
        this.bindEvents();
    }
    bindEvents() {
        $(this.modalBody)
            .find("form")
            .on("submit", (event) => {
                const formData = {
                    caseId: this.caseId,
                    hearingId: this.hearingId,
                    date: $(this.modalBody).find('input[type="date"]').val(),
                };
                event.preventDefault();
                this.changeDate(formData);
            });
    }
    render() {
        $(this.modalBody).html(`
            <form>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="3" disabled>${this.description}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Date</label>
                            <input type="date" class="form-control" value="${this.date}" required/>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        `);
    }
    changeDate(formData) {
        $.ajax({
            url: this.url,
            method: "PATCH",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            beforeSend: () => {
                $(this.modalBody)
                    .find('button[type="submit"]')
                    .html(
                        '<span class="spinner-border spinner-border-sm" role="status"></span>'
                    )
                    .prop("disabled", true);
            },
            complete: () => {
                $(this.modalBody)
                    .find('button[type="submit"]')
                    .html("Save")
                    .prop("disabled", false);
            },
            success: (response) => {
                if (typeof this.onDateChange === "function") {
                    this.onDateChange(response);
                }
                successMessage("Date changed");
            },
            error: (xhr, status, error) => {
                alert("something went wrong!");
            },
        });
    }
}
