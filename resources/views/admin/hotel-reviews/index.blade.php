@extends('layouts.admin.app')

@section('content')
@php $currentAdmin = Auth::guard('admin')->user(); @endphp
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Hotel Reviews</h3>
                <div class="card-tools">
                  @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotel-reviews.create'))
                    <button type="button" class="btn btn-primary" id="openAddReviewBtn">+ Add New</button>
                  @endif
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>SN.</th>
                      <th>Hotel</th>
                      <th>Reviewer</th>
                      <th>Rating</th>
                      <th>Date</th>
                      <th>Verified</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="reviewsTableBody">
                    @forelse ($reviews as $key=>$review)
                    <tr id="review-row-{{ $review->id }}">
                      <td>{{ $reviews->firstItem() + $key }}</td>
                      <td class="cell-hotel">{{ $review->hotel->name }}</td>
                      <td class="cell-reviewer">{{ $review->reviewer_name }}</td>
                      <td class="cell-rating">{{ $review->rating }}/10</td>
                      <td class="cell-date">{{ $review->review_date->format('d M Y') }}</td>
                      <td class="cell-verified">{{ $review->verified ? 'Yes' : 'No' }}</td>
                      <td>
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotel-reviews.edit'))
                          <button type="button" class="btn btn-primary btn-sm btn-edit-review" data-edit-url="{{ route('crm.hotel-reviews.edit', $review->id) }}">Edit</button>
                        @endif
                        @if($currentAdmin?->isAdmin() || $currentAdmin?->can('hotel-reviews.delete'))
                          <form action="{{ route('crm.hotel-reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr id="noReviewsRow">
                      <td colspan="7" class="text-center">No reviews found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $reviews->links() }}
              </div>
            </div>
          </div>
        </div>
        </div>
    </div>
</div>

<!-- Add / Edit Hotel Review Modal -->
<div class="modal fade zoom-modal" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <form id="reviewForm" action="{{ route('crm.hotel-reviews.store') }}" method="POST" data-http-method="POST" novalidate>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="reviewModalLabel">Add Review</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="rv_hotel_id">Hotel</label>
              <select name="hotel_id" id="rv_hotel_id" class="form-control" required>
                <option value="">Select a hotel</option>
                @foreach($hotels as $hotel)
                  <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                @endforeach
              </select>
              <span class="invalid-feedback d-block" data-error-for="hotel_id"></span>
            </div>
            <div class="form-group col-md-6">
              <label for="rv_reviewer_name">Reviewer Name</label>
              <input type="text" name="reviewer_name" id="rv_reviewer_name" class="form-control" placeholder="e.g. Ananya S." required>
              <span class="invalid-feedback d-block" data-error-for="reviewer_name"></span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="rv_rating">Overall Rating (0-10)</label>
              <input type="number" step="0.1" min="0" max="10" name="rating" id="rv_rating" class="form-control" required>
              <span class="invalid-feedback d-block" data-error-for="rating"></span>
            </div>
            <div class="form-group col-md-3">
              <label for="rv_review_date">Review Date</label>
              <input type="date" name="review_date" id="rv_review_date" class="form-control" required>
              <span class="invalid-feedback d-block" data-error-for="review_date"></span>
            </div>
            <div class="form-group col-md-3">
              <label for="rv_sort_order">Sort Order</label>
              <input type="number" name="sort_order" id="rv_sort_order" min="0" class="form-control" value="0">
              <span class="invalid-feedback d-block" data-error-for="sort_order"></span>
            </div>
            <div class="form-group col-md-3 d-flex align-items-end">
              <div class="custom-control custom-checkbox mb-2">
                <input type="checkbox" name="verified" value="1" class="custom-control-input" id="rv_verified" checked>
                <label class="custom-control-label" for="rv_verified">Verified Stay</label>
              </div>
            </div>
          </div>

          <label class="mb-1">Category Ratings <small class="text-muted">(0-10, optional)</small></label>
          <div class="form-row">
            <div class="form-group col-md-3">
              <label class="form-label-sm">Location</label>
              <input type="number" step="0.1" min="0" max="10" name="location_rating" class="form-control">
            </div>
            <div class="form-group col-md-3">
              <label class="form-label-sm">Cleanliness</label>
              <input type="number" step="0.1" min="0" max="10" name="cleanliness_rating" class="form-control">
            </div>
            <div class="form-group col-md-3">
              <label class="form-label-sm">Service</label>
              <input type="number" step="0.1" min="0" max="10" name="service_rating" class="form-control">
            </div>
            <div class="form-group col-md-3">
              <label class="form-label-sm">Value for Money</label>
              <input type="number" step="0.1" min="0" max="10" name="value_rating" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label for="rv_comment">Comment</label>
            <textarea name="comment" id="rv_comment" rows="3" class="form-control" placeholder="Guest review text"></textarea>
            <span class="invalid-feedback d-block" data-error-for="comment"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="reviewSubmitBtn">
            <span class="btn-label">Create Review</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    var $modal = $('#reviewModal');
    var $form = $('#reviewForm');
    var storeUrl = $form.attr('action');

    function setMode(mode) {
      if (mode === 'add') {
        $('#reviewModalLabel').text('Add Review');
        $('#reviewSubmitBtn .btn-label').text('Create Review');
        $form.attr('action', storeUrl).data('http-method', 'POST');
      } else {
        $('#reviewModalLabel').text('Edit Review');
        $('#reviewSubmitBtn .btn-label').text('Update Review');
      }
    }

    $('#openAddReviewBtn').on('click', function () {
      setMode('add');
      $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-review', function () {
      var url = $(this).data('edit-url');
      $.get(url, function (response) {
        if (!response.success) return;
        var r = response.review;
        setMode('edit');
        $form.attr('action', r.update_url).data('http-method', 'PUT');
        $form.find('[name="hotel_id"]').val(r.hotel_id);
        $form.find('[name="reviewer_name"]').val(r.reviewer_name);
        $form.find('[name="rating"]').val(r.rating);
        $form.find('[name="location_rating"]').val(r.location_rating);
        $form.find('[name="cleanliness_rating"]').val(r.cleanliness_rating);
        $form.find('[name="service_rating"]').val(r.service_rating);
        $form.find('[name="value_rating"]').val(r.value_rating);
        $form.find('[name="comment"]').val(r.comment);
        $form.find('[name="review_date"]').val(r.review_date);
        $form.find('[name="sort_order"]').val(r.sort_order);
        $form.find('[name="verified"]').prop('checked', !!r.verified);
        $modal.modal('show');
      });
    });

    FBModalForm.bind({
      form: '#reviewForm',
      modal: '#reviewModal',
      onSuccess: function (response) {
        var review = response.review;
        if ($('#review-row-' + review.id).length) {
          updateReviewRow(review);
        } else {
          appendReviewRow(review);
        }
      },
      onReset: function () {
        setMode('add');
      }
    });

    function updateReviewRow(review) {
      var $row = $('#review-row-' + review.id);
      $row.find('.cell-hotel').text(review.hotel_name);
      $row.find('.cell-reviewer').text(review.reviewer_name);
      $row.find('.cell-rating').text(review.rating + '/10');
      $row.find('.cell-date').text(review.review_date);
      $row.find('.cell-verified').text(review.verified ? 'Yes' : 'No');

      $row.addClass('row-just-updated');
      setTimeout(function () { $row.removeClass('row-just-updated'); }, 700);
    }

    function appendReviewRow(review) {
      $('#noReviewsRow').remove();

      var row = '' +
        '<tr id="review-row-' + review.id + '" class="row-just-added">' +
          '<td>#</td>' +
          '<td class="cell-hotel">' + FBModalForm.escapeHtml(review.hotel_name) + '</td>' +
          '<td class="cell-reviewer">' + FBModalForm.escapeHtml(review.reviewer_name) + '</td>' +
          '<td class="cell-rating">' + review.rating + '/10</td>' +
          '<td class="cell-date">' + review.review_date + '</td>' +
          '<td class="cell-verified">' + (review.verified ? 'Yes' : 'No') + '</td>' +
          '<td>' +
            '<button type="button" class="btn btn-primary btn-sm btn-edit-review" data-edit-url="' + review.edit_url + '">Edit</button> ' +
            '<form action="' + review.destroy_url + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this review?\');">' +
              '@csrf' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-danger btn-sm">Delete</button>' +
            '</form>' +
          '</td>' +
        '</tr>';

      $('#reviewsTableBody').prepend(row);

      $('#reviewsTableBody tr').each(function (index) {
        $(this).find('td').eq(0).text(index + 1);
      });
    }
  });
</script>
@endsection
