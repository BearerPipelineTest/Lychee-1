<div class="header__toolbar header__toolbar--photo header__toolbar--visible">

	<a wire:click="$emit('back')" class="button" id="button_back" title="{{ Lang::get('CLOSE_PHOTO') }}"
		data-tabindex="{{ Helpers::data_index_r() }}">
		<x-iconic icon="chevron-left" />
	</a>

	<a class="header__title" data-tabindex="{{ Helpers::data_index() }}">{{ $title }}</a>

	@if(AccessControl::is_logged_in())
	<a class="button button--star" id="button_star" title="{{ Lang::get('STAR_PHOTO') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="star" />
	</a>
	<a class="button button--eye" id="button_visibility" title="{{ Lang::get('VISIBILITY_PHOTO') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="eye" />
	</a>
	<a class="button button--rotate" id="button_rotate_ccwise" title="{{ Lang::get('PHOTO_EDIT_ROTATECCWISE') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="counterclockwise" />
	</a>
	<a class="button button--rotate" id="button_rotate_cwise" title="{{ Lang::get('PHOTO_EDIT_ROTATECWISE') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="clockwise" />
	</a>
	@endif
	<a class="button button--share" id="button_share" title="{{ Lang::get('SHARE_PHOTO') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic class="ionicons" icon="share-ion" />
	</a>
	<a class="button button--info" id="button_info" title="{{ Lang::get('ABOUT_PHOTO') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="info" />
	</a>
	<a class="button button--map" id="button_map" title="{{ Lang::get('DISPLAY_FULL_MAP') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="map" />
	</a>
	@if(AccessControl::is_logged_in())
	<a class="button" id="button_move" title="{{ Lang::get('MOVE') }}" data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="folder" />
	</a>
	<a class="button" id="button_trash" title="{{ Lang::get('DELETE') }}" data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="trash" />
	</a>
	@endif
	<a class="button" id="button_fs_enter" title="{{ Lang::get('FULLSCREEN_ENTER') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="fullscreen-enter" />
	</a>
	<a class="button" id="button_fs_exit" title="{{ Lang::get('FULLSCREEN_EXIT') }}"
		data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="fullscreen-exit" />
	</a>
	<a class="header__divider"></a>
	<a class="button" id="button_more" title="{{ Lang::get('MORE') }}" data-tabindex="{{ Helpers::data_index() }}">
		<x-iconic icon="ellipses" />
	</a>
</div>