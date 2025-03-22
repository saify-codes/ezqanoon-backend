$(function () {
    class NotificationHandler {
      constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.hasNextPage = true;
        this.hasUnread = false;
        this.indicatorDismissed = false; // Flag to track if the bell was clicked.
      }
  
      init() {
        this.loadNotifications(this.currentPage);
        this.bindEvents();
      }
  
      // Converts a date string to a human-readable "time ago" string.
      timeAgo(dateString) {
        const now = new Date();
        const past = new Date(dateString);
        const diffInSeconds = Math.floor((now.getTime() - past.getTime()) / 1000);
        
        if (diffInSeconds < 0) return "just now";
        if (diffInSeconds < 60)
          return `${diffInSeconds} second${diffInSeconds === 1 ? "" : "s"} ago`;
        
        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60)
          return `${diffInMinutes} minute${diffInMinutes === 1 ? "" : "s"} ago`;
        
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24)
          return `${diffInHours} hour${diffInHours === 1 ? "" : "s"} ago`;
        
        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 7)
          return `${diffInDays} day${diffInDays === 1 ? "" : "s"} ago`;
        
        if (diffInDays < 30) {
          const weeks = Math.floor(diffInDays / 7);
          return `${weeks} week${weeks === 1 ? "" : "s"} ago`;
        }
        
        if (diffInDays < 365) {
          const months = Math.floor(diffInDays / 30);
          return `${months} month${months === 1 ? "" : "s"} ago`;
        }
        
        const years = Math.floor(diffInDays / 365);
        return `${years} year${years === 1 ? "" : "s"} ago`;
      }
  
      // Builds the HTML for a single notification.
      buildNotificationHTML(notification) {
        return `
          <a href="javascript:;" class="dropdown-item mb-1 p-3 ${notification.read ? "" : "unread"}" data-id="${notification.id}">
            <div class="d-flex">
              <div class="wd-30 ht-30 d-flex align-items-center justify-content-center flex-shrink-0 bg-primary rounded-circle me-3">
                <svg width="15" height="15" viewBox="0 0 448 512">
                  <path fill="#FFF" d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416l384 0c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8l0-18.8c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"/>
                </svg>
              </div>
              <div class="flex-grow-1 me-2">
                <h6>${notification.title}</h6>
                <p class="tx-12 text-muted" style="white-space: normal">${notification.body}</p>
                <time class="tx-10 text-primary">${this.timeAgo(notification.created_at)}</time>
              </div>
            </div>
            ${notification.read ? "" : '<button class="btn btn-xs btn-secondary w-100 mt-3">Mark read</button>'}
          </a>
        `;
      }
  
      // Loads notifications via AJAX.
      loadNotifications(page) {
        if (this.isLoading || !this.hasNextPage) return;
        this.isLoading = true;
  
        $.ajax({
          url: `/notification?page=${page}`,
          type: "GET",
          dataType: "json",
          context: this, // ensures "this" inside callbacks refers to the instance
          success(response) {
            if (response.data && response.data.length > 0) {
              response.data.forEach((notification) => {
                const html = this.buildNotificationHTML(notification);
                if (notification.read) {
                  $("#notifications_container #readMessages").append(html);
                } else {
                  $("#notifications_container #unreadMessages").append(html);
                  this.hasUnread = true;
                }
              });
  
              // Only update the indicator if it hasn't been dismissed manually.
              if (!this.indicatorDismissed && this.hasUnread) {
                  $(".indicator").removeClass("d-none");
              }
  
              if (response.next_page_url) {
                this.currentPage++;
              } else {
                this.hasNextPage = false;
                $("#notifications_container").off("scroll");
              }
            } else {
              this.hasNextPage = false;
              $("#notifications_container").append('<p class="p-3">You are all caught up!</p>');
              $("#notifications_container").off("scroll");
            }
          },
          error(xhr, status, error) {
            console.error("Error loading notifications:", error);
          },
          complete: () => {
            this.isLoading = false;
          },
        });
      }
  
      // Marks a notification as read.
      markAsRead(notificationId) {
        $.ajax({
          url: `/notification/${notificationId}`,
          type: "PATCH",
          dataType: "json",
          data: {
            _token: document.querySelector('meta[name="_token"]').content,
          },
          error(xhr, status, error) {
            console.error("Error marking notification as read:", error);
          },
        });
      }
  
      // Binds event listeners.
      bindEvents() {
        // Mark notification as read on button click.
        $("#notifications_container").on("click", ".btn-secondary", (e) => {
          const $notificationItem = $(e.currentTarget).closest("a.dropdown-item");
          const notificationId = $notificationItem.data("id");
          $notificationItem.removeClass("unread");
          $(e.currentTarget).remove();
          $("#notifications_container #readMessages").prepend($notificationItem);
          this.markAsRead(notificationId);
        });
  
        // Toggle notification dropdown and permanently hide the indicator.
        $("#notificationDropdown").on("click", () => {
          $("#notificationDropdown + .dropdown-menu").toggleClass("show");
          $(".indicator").addClass("d-none");
          this.indicatorDismissed = true; // Set the flag so the indicator won't show again.
        });
  
        // Infinite scroll.
        $("#notifications_container").on("scroll", () => {
          const $container = $("#notifications_container");
          if ($container.scrollTop() + $container.height() >= $container[0].scrollHeight - 100) {
            this.loadNotifications(this.currentPage);
          }
        });
      }
    }
  
    // Create and initialize the notification handler.
    const notificationHandler = new NotificationHandler();
    notificationHandler.init();
  });
  