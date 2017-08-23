<li data-id="{{$list->id}}">
	<a href="{{route('list', ['id' => $list->id, 'name' => str_slug($list->name)])}}">
		<span class="item-count">{{$list->movies_count}}</span>
		<span>
			@if ($list->ordered) 
				<i class="icon-ordered fa fa-sort-numeric-asc"></i>
			@endif
			{{$list->name}}
		</span>
	</a>
</li>
