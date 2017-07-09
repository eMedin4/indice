<div class="modal-wrap modal-wrap-add-to-list">
	<div class="modal">
		<div class="modal-inner">
			<div class="header">
				<h3>Añadir a mis listas</h3>
				<i class="modal-close propagation fa fa-times"></i>
			</div>
			<ul class="add-to-list" data-movie="{{$movie->id}}" data-url="{{route('addlist')}}" data-alturl="{{route('extractlist')}}">
				@foreach ($lists as $list)
					<li>
						@if ($list->movies->where('id', $movie->id)->count())
							<div class="lbl-check add-to-list-disable" data-id="{{$list->id}}" data-ordered="{{$list->ordered}}">
						@else
							<div class="lbl-check add-to-list-active" data-id="{{$list->id}}" data-ordered="{{$list->ordered}}">
						@endif
								{{$list->name}}
								<span class="item-count">{{$list->movies->count()}}</span>
							</div>
					</li>
				@endforeach
			</ul>
			<span class="link btn-new-list" data-position="add-to-list">Crear nueva lista<i class="fa fa-add-to-list"></i></span>
		</div>
	</div>
</div>


