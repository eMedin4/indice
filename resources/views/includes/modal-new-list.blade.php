<div class="modal-wrap modal-wrap-new-list">
	<div class="modal">
		<div class="modal-inner">
			<div class="header">
				<h3>Crear nueva lista</h3>
				<i class="modal-close propagation fa fa-times"></i>
			</div>
			<form method="POST" class="form-new-list" data-actionnew="{{route('newlist')}}" data-actionedit="{{route('posteditlist')}}" data-movie="{{$movie->id or 0}}">
	            {{ csrf_field() }}
	            <div class="errors"></div>
	            <input type="hidden" name="position" class="position">
	            <input type="text" name="name" maxlength="64" placeholder="Nombre">           
	            <textarea name="description" rows="3" maxlength="2000" placeholder="Descripción"></textarea>
	            <div class="checkbox">
	                <input id="check-description" type="checkbox" name="check-description">
	                <label class="lbl-check" for="check-description">Añadir descripción</label>
	            </div>
	            <div class="checkbox">
	                <input id="check-ordered" type="checkbox" name="check-ordered">
	                <label class="lbl-check" for="check-ordered">Lista numerada</label>
	            </div>
	            <div class="btn-group">
	                <button type="submit" class="link">Crear</button>
	                <button type="button" class="link link-dark propagation">Cancelar</button>
	            </div>
	        </form>
		</div>
	</div>
</div>
