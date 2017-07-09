<div class="loop margins">
	@foreach ($items as $list)
		<article>

			<a href="{{route('list', ['id' => $list->id, 'name' => str_slug($list->name)])}}" class="title">{{$list->name}}</a>
			<p>
			@if ($list->movies->count())
				{{$list->movies->count()}} películas · actualizada hace {{$list->updated_at->diffForHumans()}}
			@else
				no hay peliculas
			@endif
			</p>
			
		</article>
	@endforeach
</div>
