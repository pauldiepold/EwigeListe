@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Ewige Liste')

@section('content')
	@include('include.back')
<div class="">
<table id="profilesTable" class="table table-striped nowrap myDataTable w-100">
	<thead>
		<tr>
			<th>
				Name
			</th>
			@foreach($columns as $column)
			<th>
				{{ $column->get(0) }}
			</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($profiles as $profile)
			<tr{!! $profile->player->id == Auth::user()->player->id ? ' class="bg-primary-light"' : ''!!}>
				<td>
					<a href="profiles/{{ $profile->player->id }}">
						{{ $profile->player->surname }}
					</a>
				</td>
				@foreach($columns as $column)
					<td>
						{{ $profile->{$column->get(1)} }}
					</td>
				@endforeach
			</tr>		
		@endforeach
	</tbody>
</table>
</div>

@endsection

@push('scriptsHead')
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.6/css/fixedColumns.bootstrap4.min.css">
	<style>
		table.dataTable thead>tr>th {
		}
		table.dataTable thead>tr>th.sorting,
		table.dataTable thead>tr>th.sorting_asc,
		table.dataTable thead>tr>th.sorting_desc {
			padding-right: 1.35rem;
		}
		table.dataTable thead .sorting:before,
		table.dataTable thead .sorting:after,
		table.dataTable thead .sorting_asc:before,
		table.dataTable thead .sorting_asc:after,
		table.dataTable thead .sorting_desc:before,
		table.dataTable thead .sorting_desc:after,
		table.dataTable thead .sorting_asc_disabled:before,
		table.dataTable thead .sorting_asc_disabled:after,
		table.dataTable thead .sorting_desc_disabled:before,
		table.dataTable thead .sorting_desc_disabled:after{
			bottom: 0.2em;
		}
		table.dataTable thead th.sorting:before {
    		content: none;
		}
		table.dataTable thead th.sorting_asc:before {
    		content: none;
		}
		table.dataTable thead th.sorting_desc:before {
    		content: none;
		}
		table.dataTable thead th.sorting::after {		
    		font-family: "Font Awesome 5 Free"; font-weight: 400; content: "\f0dc";
			opacity: 0.3;
  		}		
		table.dataTable thead th.sorting_asc::after {		
    		font-family: "Font Awesome 5 Free"; font-weight: 400; content: "\f0de";
			opacity: 1;
  		}		
		table.dataTable thead th.sorting_desc::after {		
    		font-family: "Font Awesome 5 Free"; font-weight: 400; content: "\f0dd";
			opacity: 1;
  		}
		table.DTFC_Cloned tr,
		table.dataTable.table-striped.DTFC_Cloned tbody{
			background-color: #f1f1ef;
		}
		th.sorting_desc, th.sorting_asc {
			color: #d16341;
		}
		div.dataTables_wrapper div.dataTables_paginate ul.pagination {
			justify-content: center;
	</style>
@endpush

@push('scripts')
	<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>

    <script>
		$(document).ready(function() {	
			$('#profilesTable').DataTable({					
        		scrollX:        true,
        		scrollCollapse: true,
				fixedColumns: true,
				paging: false,
				dom: 't<"my-3"p><"my-3"l>',
				info: false,
				searching: false,
        		order: [[ 1, "desc" ]],				
        		language: {
            		lengthMenu: "Zeige _MENU_ pro Seite",
            		info: "Seite _PAGE_ von _PAGES_",
    				paginate: {
        				next:       "Weiter",
        				previous:   "Zurück"
   					},
        		}
			});
		} );
    </script>
@endpush