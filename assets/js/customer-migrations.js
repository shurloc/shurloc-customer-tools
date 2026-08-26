document.addEventListener( 'DOMContentLoaded', () => {

	document
		.querySelectorAll( '.shurloc-migration-form' )
		.forEach( ( form ) => {

			const checkbox = form.querySelector(
				'.shurloc-migration-enable'
			);

			const button = form.querySelector(
				'.shurloc-migration-submit'
			);

			if ( ! checkbox || ! button ) {
				return;
			}

			checkbox.addEventListener( 'change', () => {
				button.disabled = ! checkbox.checked;
			} );

			form.addEventListener( 'submit', ( event ) => {

				if ( ! checkbox.checked ) {
					event.preventDefault();
					return;
				}

				const message =
					form.dataset.confirmMessage ||
					'Run this migration?';

				if ( ! window.confirm( message ) ) {
					event.preventDefault();
					checkbox.checked = false;
					button.disabled = true;
				}
			} );
		} );
} );
