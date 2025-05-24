class BaseModal{

    constructor(){
        this.onDateChange    = null;
        this.bsModalInstance = null; // To hold the Bootstrap Modal instance
        this.modal           = null;
    }

    render(){
        throw Error("implement render method in child class")
    }

    bindEvents(){
        throw Error("implement bindEvents method in child class")
    }

    open() {
        this.render();
        this.bindEvents();
        this.bsModalInstance = new bootstrap.Modal(this.modal);
        this.bsModalInstance.show();
    }

    close() {
        this.bsModalInstance.hide();
        $(this.modal).remove();
    }

}

class HearingModal extends BaseModal{
    constructor(caseId, caseName, hearingId, description, date) {
        super()
        this.caseId          = caseId;
        this.caseName        = caseName;
        this.hearingId       = hearingId;
        this.description     = description;
        this.date            = date;
        this.url             = `/firm/cases/${caseId}/hearing/${hearingId}`;
        this.modalId         = `hearing-modal-${hearingId}`; // Unique ID for this modal instance
    }

    render() {
        this.modal = $(`
            <div class="modal fade" id="${this.modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Hearing Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Case</label>
                                    <input class="form-control" name="name" id="name" value="${this.caseName}" disabled/>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" disabled>${this.description}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="hearing-date" class="form-label">Date</label>
                                    <input type="date" class="form-control" name="date" id="hearing-date" value="${this.date}" required />
                                </div>
                                <div class="mb-3">
                                    <a href="/firm/manage/cases/${this.caseId}" type="submit" class="btn btn-warning me-2">View case</a>
                                    <button type="submit" class="btn btn-primary me-2">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `)
        $("body").prepend(this.modal);
    }

    bindEvents() {
        $(this.modal)
            .find("form")
            .on("submit", (event) => {
                event.preventDefault();
                this.changeDate(event.target);
            });
    }

    changeDate(form) {
        $.ajax({
            url: this.url,
            method: "PATCH",
            data: $(form).serialize(),
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            beforeSend: () => {
                $(this.modal)
                    .find('button[type="submit"]')
                    .html('<span class="spinner-border spinner-border-sm" role="status"></span>')
                    .prop("disabled", true);
            },
            complete: () => {
                $(this.modal)
                    .find('button[type="submit"]')
                    .html("Save")
                    .prop("disabled", false);
            },
            success: (response) => {
                if (typeof this.onDateChange === "function") {
                    this.onDateChange(response);
                }
                successMessage("Date changed successfully.");
            },
            error: (xhr, status, error) => {
                alert("Something went wrong!");
            },
        });
    }
}

class AppointmentModal extends BaseModal{
    constructor(appointmentId, name, details, meetingDate) {
        super()
        this.appointmentId   = appointmentId;
        this.name            = name;
        this.details         = details;
        this.meetingDate     = meetingDate;
        this.modalId         = `appointment-modal-${appointmentId}`; // Unique ID for this modal instance
    }

    render() {
        this.modal = $(`
            <div class="modal fade" id="${this.modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Appointment Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control" name="name" id="name" value="${this.name}" disabled/>
                                </div>
                                <div class="mb-3">
                                    <label for="time" class="form-label">Time</label>
                                    <input class="form-control" name="time" id="time" value="${new Date(this.meetingDate).toLocaleTimeString()}" disabled/>
                                </div>
                                <div class="mb-3">
                                    <label for="details" class="form-label">Details</label>
                                    <textarea class="form-control" name="details" id="details" rows="3" disabled>${this.details}</textarea>
                                </div>
                                <div class="mb-3">
                                    <a href="/firm/manage/appointments/${this.appointmentId}" type="submit" class="btn btn-warning me-2">View appointment</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `)
        $("body").prepend(this.modal);
    }

    bindEvents() {}

}

class MyEventModal extends BaseModal{
    constructor(eventId, description, deadline) {
        super()
        this.eventId        = eventId;
        this.description    = description;
        this.modalId        = `myevents-modal-${eventId}`; // Unique ID for this modal instance
    }

    render() {
        this.modal = $(`
            <div class="modal fade" id="${this.modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Event Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" disabled>${this.description}</textarea>
                                </div>
                                <div class="mb-3">
                                    <a href="/firm/calendar/${this.eventId}" type="submit" class="btn btn-warning me-2">View event</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `)
        $("body").prepend(this.modal);
    }

    bindEvents() {}
}
