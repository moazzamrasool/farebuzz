<div class="widget">
    <h4 class="widget-title line-bottom">Quick Contact</h4>
    <form id="query_form">
        @csrf
      <div class="form-group">
        <input name="name" class="form-control" type="text" required="" placeholder="Enter Name">
      </div>
      <div class="form-group">
        <input name="email" class="form-control" type="text" required="" placeholder="Enter Email">
      </div>
      <div class="form-group">
        <input name="phone" class="form-control" type="text" required="" placeholder="Enter Phone">
      </div>
      <input name="subject" class="form-control" value="Contact from Page" type="hidden" required="" placeholder="Enter Phone">

      <div class="form-group">
        <textarea name="message" class="form-control" required="" placeholder="Enter Message" rows="3"></textarea>
      </div>
      <div class="form-group">
        <button type="submit"  class="btn btn-danger btn-flat btn-xs btn-quick-contact text-gray pt-5 pb-5 button_submit">Send Message</button>
      </div>
    </form>
  </div>


  <script>   
    // Get CSRF token from meta tag
    $('#query_form').submit(function(e){
      e.preventDefault();
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $('.button_submit').text('Submitting....')
      // Data to send
      let formdata = new FormData(this)
      const data = {
          name: formdata.get('name'),
          email: formdata.get('email'),
          subject: formdata.get('subject'),
          message: formdata.get('message')
      };
      
  
      // Send AJAX POST request
      fetch('{{ route("contact") }}', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
          },
          body: JSON.stringify(data)
      })
      .then(response => {
          if (!response.ok) throw new Error('Network response was not OK');
          return response.json(); // or .text() if you return plain text
      })
      .then(data => {
          console.log('Success:', data);
          // alert('Message sent successfully!');
          // toastr.success(data.message);
          $('#query_form')[0].reset();
          $('.button_submit').text('Submit');
          // setTimeout(() => {
            window.location.href ='{{ route("thank_you") }}';
          // }, 3000);
      })
      .catch(error => {
          console.error('Error:', error);
          // alert('There was an error sending.');
          toastr.error(error.message);
          $('.button_submit').text('Submit')
      });
  });
// include all page quesy wali
  </script>


