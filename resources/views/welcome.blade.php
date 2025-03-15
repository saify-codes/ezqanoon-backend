
<x-lawyer.app>

   
      <h1>GLightbox Example</h1>
      
      <!-- Image link (grouped with data-gallery for a gallery effect) -->
      <a href="https://via.placeholder.com/800x600" class="glightbox" data-gallery="gallery1">
        <img src="https://via.placeholder.com/150x100" alt="Placeholder Image">
      </a>
    
      <!-- Another image in the same gallery -->
      <a href="https://via.placeholder.com/800x600?text=Second" class="glightbox" data-gallery="gallery1">
        <img src="https://via.placeholder.com/150x100?text=Second" alt="Second Placeholder Image">
      </a>
    
      <!-- Video link -->
      <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="glightbox">
        Watch Video
      </a>
    
      <!-- GLightbox JS from CDN -->
      <script>
        // Initialize GLightbox
        const lightbox = GLightbox({
          selector: '.glightbox'
        });
      </script> 

</x-lawyer.app>
