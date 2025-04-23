$(function () {
  class Inbox {
    messages    = []
    currentPage = 1
    nextPage    = true
    container   = null
    loadMoreBtn = null

    constructor(node) {
      this.container = node
      this.init()
    }

    init() {
      this.bindEvents()
      this.fetchMessages()
    }

    bindEvents() {
      // Event delegation for handling "read" button click
      $(this.container).on('click', '.btn-read', (e) => {
        const messageId = $(e.target).data('id'); // Get the message ID from data attribute
        this.markRead(messageId, e.target); // Call the markRead function
      });
      
      $(this.container).on('click', '#load-messages', (e) => {
        this.fetchMessages()
      });
    }

    fetchMessages() {
      $.ajax({
        url: `/notification?page=${this.currentPage}`,
        type: "GET",
        dataType: "json",
        context: this,
        beforeSend(){
          this.loadMoreBtn
          ?.prop('disabled', true)
          .html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`)
        },
        complete(){
          this.loadMoreBtn
          ?.prop('disabled', false)
          .html(`Load more`)
        },
        success(response) {

          if (response.data.length > 0) {
            this.messages.push(...response.data)
            this.nextPage = !!response.next_page_url
          }

          if (response.next_page_url) {
            if (!this.loadMoreBtn) {
              this.loadMoreBtn = $('<button class="btn btn-sm btn-outline-primary" id="load-messages">Load more</button>');
              $(this.container).append(this.loadMoreBtn);
            }
          } else {
            this.loadMoreBtn?.remove();
          }

          this.render()
          this.currentPage++
        },
        error(xhr, status, error) {
          alert('Something went wrong!')
          console.error("Error loading messages:", error);
        },
      });
    }

    markRead(id, buttonElement) {      
      $(buttonElement).remove();
    }

    render() {

      if (this.messages.length === 0) {
        $(this.container).html(`
          <a href="javascript:;" class="d-flex align-items-center pb-3">
            <div class="p-5">
              <img class="img-fluid" src="/assets/images/others/no-message.svg" alt="no message">
              <p class="text-secondary text-center mt-4 font-bold">No messages </p>
            </div>
          </a>
        `)
        return
      }

      let messageList = '';
      $.each(this.messages, (index, message) => {
        messageList += `
          <a href="javascript:;" class="d-flex align-items-center border-bottom py-3">
            <div class="me-3">
              <img width="35" src="https://www.nobleui.com/html/template/assets/images/faces/face2.jpg" class="rounded-circle" alt="user">
            </div>
            <div class="w-100">
              <div class="d-flex justify-content-between">
                <h6 class="text-body mb-2">${message.title}</h6>
                <p class="text-secondary fs-12px">12.30 PM</p>
              </div>
              <p class="text-secondary fs-13px">${message.body}</p>
              <!-- <button class="btn btn-sm btn-primary float-end btn-read" data-id="${message.id}">read</button> -->
            </div>
          </a>
        `;
      });

      $(this.container).find('#messages').html(messageList);
    }
  }

  new Inbox(document.getElementById('inbox'))
});
