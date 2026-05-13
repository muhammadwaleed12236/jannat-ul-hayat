@extends('admin_panel.layout.app')
@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="mb-0">Inventory On-Hand</h5>
    <a href="{{ route('product') }}" class="btn btn-sm btn-outline-secondary">Back to Products</a>
  </div>

  <div class="card">
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Code</th>
              <th>Name</th>
              <th>Brand</th>
              <th>UOM</th>
              <th class="text-end">On-Hand</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $i => $r)
              <tr>
                <td>{{ $i+1 }}</td>
                <td class="text-muted">{{ $r->item_code }}</td>
                <td>{{ $r->item_name }}</td>
                <td>{{ $r->brand_name }}</td>
                <td>{{ $r->unit_name }}</td>
                <td class="text-end">{{ rtrim(rtrim(number_format($r->onhand_qty, 3, '.', ''), '0'), '.') }}</td>
                <td class="text-center">
                  <button type="button" class="btn btn-xs btn-info viewBreakdownBtn" 
                      data-id="{{ $r->id }}" data-name="{{ $r->item_name }}">
                      📍 Loc
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted">No data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
  <!-- Stock Breakdown Modal -->
  <div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
              <div class="modal-header bg-info text-white" style="border-radius: 12px 12px 0 0;">
                  <h5 class="modal-title fw-bold"><i class="fas fa-map-marker-alt me-2"></i> Stock Breakdown</h5>
                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body p-0">
                  <div class="p-3 bg-light border-bottom">
                      <h6 id="breakdown_product_name" class="mb-0 fw-bold text-dark">Product Name</h6>
                  </div>
                  <div class="table-responsive">
                      <table class="table table-sm table-hover mb-0">
                          <thead class="table-light">
                              <tr>
                                  <th class="ps-3">Warehouse/Shop</th>
                                  <th>Location</th>
                                  <th class="text-center">Boxes</th>
                                  <th class="text-end pe-3">Pieces</th>
                              </tr>
                          </thead>
                          <tbody id="breakdown_body">
                              <!-- Ajax Load -->
                          </tbody>
                      </table>
                  </div>
                  <div id="breakdown_loading" class="text-center py-4 d-none">
                      <div class="spinner-border text-info" role="status"></div>
                  </div>
                  <div id="breakdown_empty" class="text-center py-4 d-none text-muted">
                      No stock found in any location.
                  </div>
              </div>
              <div class="modal-footer bg-light border-0" style="border-radius: 0 0 12px 12px;">
                  <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>

@endsection

@section('js')
<script>
    $(document).on('click', '.viewBreakdownBtn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#breakdown_product_name').text(name);
        $('#breakdown_body').empty();
        $('#breakdown_loading').removeClass('d-none');
        $('#breakdown_empty').addClass('d-none');
        $('#breakdownModal').modal('show');

        $.get(`/warehouse-stock/breakdown/${id}`, function(data) {
            $('#breakdown_loading').addClass('d-none');
            
            if (data.length === 0) {
                $('#breakdown_empty').removeClass('d-none');
            } else {
                let html = '';
                data.forEach(s => {
                    html += `
                    <tr>
                        <td class="ps-3 font-weight-bold">${s.warehouse}</td>
                        <td><span class="badge bg-light text-dark border">${s.location || '--'}</span></td>
                        <td class="text-center text-primary font-weight-bold">${s.boxes}</td>
                        <td class="text-end pe-3 text-success font-weight-bold">${s.total_pieces.toLocaleString()}</td>
                    </tr>`;
                });
                $('#breakdown_body').html(html);
            }
        });
    });
</script>
@endsection
