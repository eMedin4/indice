
<div class="modal-wrap modal-wrap-filters">
	<div class="modal">
		<div class="modal-inner">

			<div class="header">
				<h3>Filtra las películas</h3>
				<i class="modal-close propagation fa fa-times"></i>
			</div>

			<form class="form-filter" method="GET">
				<div class="filter-block">
					<div class="label">Orden</div>
					<select name="order">
					  <option value="time" selected="selected">Fecha de emisión</option>
					  <option value="movies.year">Año</option>
					  <option value="movies.fa_rat">Puntuación</option>
					</select>
				</div>
				<div class="filter-block filter-block-duo">
					<div class="label">Año</div>
					<div>
						<select name="fromyear" id="fromyear">
							<option disabled selected>Desde</option>
							@for ($i = 2018; $i > 1900; $i--)
								<option value="{{$i}}">{{$i}}</option>
							@endfor
						</select><!-- 
						 --><select name="toyear" id="toyear">
						 	<option disabled selected>Hasta</option>
							@for ($i = 2019; $i > 1901; $i--)
								<option value="{{$i}}">{{$i}}</option>
							@endfor
						</select>
					</div>
				</div>
				<div class="filter-block filter-block-duo">
					<div class="label">Nota<span class="label-detail">*Estrellas</span></div>
					<div>
						<select name="fromnote" id="fromnote">
							<option disabled selected>Desde</option>
							@for ($i = 0; $i < 6; $i++)
								<option value="{{$i}}">{{$i}}</option>
							@endfor
						</select><!-- 
						 --><select name="tonote" id="tonote">
						 	<option disabled selected>Hasta</option>
							@for ($i = 0; $i < 6; $i++)
								<option value="{{$i}}">{{$i}}</option>
							@endfor
						</select>
					</div>
				</div>
				@if (Route::is('tv'))
					<div>
						<h3 class="btn-channel-dropdown">Filtra por canal <i class="icon-channel-dropdown fa fa-keyboard-arrow-down"></i></h3>
						<div class="channel-group">
							<div>
								<label><input type="checkbox" id="select-all">Todas</label>
							</div>	
							<div>
								<label><input type="checkbox" name="channel[]" value="TVE" checked>La 1</label>
									<label><input type="checkbox" name="channel[]" value="LA2" checked>La 2</label>
							</div>							
							<div>
								<label><input type="checkbox" name="channel[]" value="C4" checked>Cuatro</label>
									<label><input type="checkbox" name="channel[]" value="T5" checked>Telecinco</label>
							</div>
							<div>
								<label><input type="checkbox" name="channel[]" value="A3" checked>Antena 3</label>
									<label><input type="checkbox" name="channel[]" value="SEXTA" checked>La Sexta</label>
							</div>
							<div>
								<label><input type="checkbox" name="channel[]" value="MV3" checked>#0</label>
									<label><input type="checkbox" name="channel[]" value="MV1" checked>Mov Estrenos</label>
							</div>							
							<div>
								<label><input type="checkbox" name="channel[]" value="CPCOLE" checked>Mov DCine</label>
									<label><input type="checkbox" name="channel[]" value="CPACCI" checked>Mov Acción</label>
							</div>
							<div>
								<label><input type="checkbox" name="channel[]" value="CPCOME" checked>Mov Comedia</label>
									<label><input type="checkbox" name="channel[]" value="CPXTRA" checked>Mov Xtra</label>
							</div>							
							<div>
								<label><input type="checkbox" name="channel[]" value="AMC" checked>AMC</label>
									<label><input type="checkbox" name="channel[]" value="AXN" checked>AXN</label>
							</div>
							<div>
								<label><input type="checkbox" name="channel[]" value="SET" checked>Axn White</label>
									<label><input type="checkbox" name="channel[]" value="COSMO" checked>Cosmo</label>
							</div>							
							<div>
								<label><input type="checkbox" name="channel[]" value="CL13" checked>Calle 13</label>
									<label><input type="checkbox" name="channel[]" value="PCM" checked>Comedy Central</label>
							</div>
							<div>
								<label><input type="checkbox" name="channel[]" value="DCH" checked>Disney Ch</label>
									<label><input type="checkbox" name="channel[]" value="DIVINI" checked>Divinity</label>
							</div>							
							<div>
								<label><input type="checkbox" name="channel[]" value="FOXGE" checked>Fox</label>
									<label><input type="checkbox" name="channel[]" value="FOXCR" checked>Fox Life</label>
							</div>
							<div>
								<label><input type="checkbox" name="channel[]" value="HOLLYW" checked>Hollywood</label>
									<label><input type="checkbox" name="channel[]" value="NEOX" checked>Neox</label>
							</div>							
							<div>
								<label><input type="checkbox" name="channel[]" value="NOVA" checked>Nova</label>
									<label><input type="checkbox" name="channel[]" value="PARCH" checked>Paramount Ch</label>
							</div>
							<div>
								<label><input type="checkbox" name="channel[]" value="SCI-FI" checked>SyFy</label>
									<label><input type="checkbox" name="channel[]" value="TCM" checked>TCM</label>
							</div>							
							<div>
								<label><input type="checkbox" name="channel[]" value="TNT" checked>TNT</label>
							</div>
						</div>
					</div>
				@endif
				<div class="btn-group">
					<button type="submit" class="link">Aceptar</button>
					<button type="button" class="link link-dark propagation">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- SI HAY FILTROS LOS RELLENAMOS POR DEFECTO -->
<script>
	@if (isset($filters['fromyear']))
	document.getElementById('fromyear').value={{$filters['fromyear']}};
	@endif
	@if (isset($filters['toyear']))
	document.getElementById('toyear').value={{$filters['toyear']}};
	@endif
	@if (isset($filters['fromnote']))
	document.getElementById('fromnote').value={{$filters['fromnote']}};
	@endif
	@if (isset($filters['tonote']))
	document.getElementById('tonote').value={{$filters['tonote']}};
	@endif
</script>
