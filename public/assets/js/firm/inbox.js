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
      $(this.container).on('click', '.read-message', (e) => {
        const messageId = $(e.target).data('id');
        this.markRead(messageId);
        $(e.target).closest('.message').removeClass('unread');
        $(e.target).remove();
      });
      
      $(this.container).on('click', '#load-messages', (e) => {
        this.fetchMessages()
      });
    }

    fetchMessages() {
      $.ajax({
        url: `/firm/notification?page=${this.currentPage}`,
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
              this.loadMoreBtn = $('<button class="btn btn-sm btn-outline-primary mt-2" id="load-messages">Load more</button>');
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

    markRead(messageId) {   
      $.ajax({
        url: `/firm/notification/${messageId}`,
        type: "PATCH",
        dataType: "json",
        data: {
          _token: document.querySelector('meta[name="_token"]').content,
        },
        success(){
          successMessage('Message read')
        },
        error(xhr, status, error) {
          errorMessage('Couldn;t read message')
          console.error("Error marking notification as read:", error);
        },
      });   
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

      let messageList = this.messages.map(message => `
          <div class="message d-flex align-items-center border-bottom p-3 ${!message.read ? 'unread' : ''}">
            <div class="me-3">
              ${message.icon}
            </div>
            <div class="w-100">
              <div class="d-flex justify-content-between">
                <h6 class="text-body mb-2">${message.title}</h6>
                <p class="text-secondary fs-12px">${this.prettyDateTime(message.created_at)}</p>
              </div>
              <p class="text-secondary fs-13px">${message.body}</p>
              ${!message.read? `<a href="javascript:0" class="read-message" data-id="${message.id}">mark read</a>` : `` }
            </div>
          </div>
        `);

      $(this.container).find('#messages').html(messageList.join('\n'));
    }

    prettyDateTime(dateString) {
      const now           = new Date();
      const past          = new Date(dateString);
      const diffInSeconds = Math.floor((now.getTime() - past.getTime()) / 1000);
      
      if (diffInSeconds < 0)        return "just now";
      if (diffInSeconds < 60)       return `${diffInSeconds}                        second  ${diffInSeconds                       === 1 ? '' : 's'} ago`;
      if (diffInSeconds < 3600)     return `${Math.floor(diffInSeconds/60)}         minute  ${Math.floor(diffInSeconds/60)        === 1 ? '' : 's'} ago`;
      if (diffInSeconds < 86400)    return `${Math.floor(diffInSeconds/3600)}       hour    ${Math.floor(diffInSeconds/3600)      === 1 ? '' : 's'} ago`;
      if (diffInSeconds < 604800)   return `${Math.floor(diffInSeconds/86400)}      day     ${Math.floor(diffInSeconds/86400)     === 1 ? '' : 's'} ago`;
      if (diffInSeconds < 2592000)  return `${Math.floor(diffInSeconds/604800)}     week    ${Math.floor(diffInSeconds/604800)    === 1 ? '' : 's'} ago`;
      if (diffInSeconds < 31536000) return `${Math.floor(diffInSeconds/2592000)}    month   ${Math.floor(diffInSeconds/2592000)   === 1 ? '' : 's'} ago`;
                                    return `${Math.floor(diffInSeconds/31536000)}   year    ${Math.floor(diffInSeconds/31536000)  === 1 ? '' : 's'} ago`;
    }
  }

  new Inbox(document.getElementById('inbox'))
});
