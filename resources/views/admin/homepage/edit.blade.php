@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ $section->name }}</h3>
                <div class="card-tools">
                  <a href="{{ route('crm.homepage-sections.index') }}" class="btn btn-secondary btn-sm">&larr; All Sections</a>
                </div>
              </div>
              <div class="card-body">
                <form action="{{ route('crm.homepage-sections.update', $section->key) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Heading</label>
                      <input type="text" name="heading" class="form-control" value="{{ old('heading', $section->heading) }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Subheading</label>
                      <input type="text" name="subheading" class="form-control" value="{{ old('subheading', $section->subheading) }}">
                    </div>
                  </div>

                  @if(!empty($config['extra_fields']))
                    <div class="form-row">
                      @foreach($config['extra_fields'] as $field)
                        @if($field === 'blurb')
                          <div class="form-group col-md-12">
                            <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                            <textarea name="extra[{{ $field }}]" rows="2" class="form-control">{{ old("extra.$field", $section->extra[$field] ?? '') }}</textarea>
                          </div>
                        @else
                          <div class="form-group col-md-4">
                            <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                            <input type="text" name="extra[{{ $field }}]" class="form-control" value="{{ old("extra.$field", $section->extra[$field] ?? '') }}">
                          </div>
                        @endif
                      @endforeach
                    </div>
                  @endif

                  @if(($config['group']['type'] ?? null) === 'tabs')
                    <hr>
                    <h6>Tabs</h6>
                    <p class="text-muted">Manage the sub-tabs shown for this section (e.g. Beach / Culture / Ski). Each card below is assigned to one of these tabs.</p>
                    <div data-repeater id="tabsRepeater">
                      <div data-repeater-rows>
                        @foreach($section->extra['tabs'] ?? [] as $i => $tab)
                          @include('admin.homepage._tab_row', ['index' => $i, 'tab' => $tab])
                        @endforeach
                      </div>
                      <template data-repeater-template>
                        @include('admin.homepage._tab_row', ['index' => '__INDEX__', 'tab' => null])
                      </template>
                      <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add>+ Add Tab</button>
                    </div>
                  @endif

                  @if(!empty($config['data_source']))
                    <hr>
                    <h5>Items</h5>
                    <p class="text-muted">This section pulls its cards live from real data — items are selected automatically (featured first, then best-seller, then most recent) and update on their own whenever the underlying records change. Use Item Limit to control how many show.</p>
                    <div class="form-group col-md-4 px-0">
                      <label>Item Limit</label>
                      <input type="number" name="item_limit" class="form-control" min="1" max="50" value="{{ old('item_limit', $section->item_limit ?? 10) }}">
                    </div>
                  @else
                    <hr>
                    <h5>Cards</h5>
                    <div data-repeater id="itemsRepeater">
                      <div data-repeater-rows>
                        @foreach($section->items as $i => $item)
                          @include('admin.homepage._item_row', ['index' => $i, 'item' => $item, 'config' => $config, 'section' => $section, 'coupons' => $coupons])
                        @endforeach
                      </div>
                      <template data-repeater-template>
                        @include('admin.homepage._item_row', ['index' => '__INDEX__', 'item' => null, 'config' => $config, 'section' => $section, 'coupons' => $coupons])
                      </template>
                      <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add>+ Add Card</button>
                    </div>
                  @endif

                  <hr>
                  <button type="submit" class="btn btn-primary">Save Section</button>
                </form>
              </div>
            </div>

            @if($section->key === 'hero')
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Search Tabs</h3>
                  <div class="card-tools">
                    <small class="text-muted">Toggle which hero search tabs are live. Disabled tabs show greyed-out with their badge text.</small>
                  </div>
                </div>
                <div class="card-body table-responsive p-0">
                  <form action="{{ route('crm.search-tab-settings.update') }}" method="POST">
                    @csrf
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Tab</th>
                          <th>Enabled</th>
                          <th>Badge Text</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($searchTabs as $i => $tab)
                          <tr>
                            <input type="hidden" name="tabs[{{ $i }}][tab_key]" value="{{ $tab->tab_key }}">
                            <td>{{ $tab->label }}</td>
                            <td>
                              <input type="checkbox" name="tabs[{{ $i }}][enabled]" value="1" {{ $tab->enabled ? 'checked' : '' }}>
                            </td>
                            <td>
                              <input type="text" name="tabs[{{ $i }}][badge_text]" class="form-control form-control-sm" value="{{ $tab->badge_text }}" placeholder="e.g. Coming soon">
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary">Save Search Tabs</button>
                    </div>
                  </form>
                </div>
              </div>
            @endif

          </div>
        </div>
        </div>
    </div>
</div>
@endsection
