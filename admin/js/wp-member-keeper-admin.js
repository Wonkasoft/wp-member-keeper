(function( $ ) {
	'use strict';

	if ( document.querySelector( '.tab-item' ) ) {
		var tabs = document.querySelectorAll( '.tab-item' );

		tabs.forEach( function( tab, i ) {
			tab.addEventListener( 'click', function( e ) {
				var target = this;
				var data_target = target.getAttribute( 'data-target' );
				var current_active_tab = document.querySelector( '.tab-item.active' );
				var current_active_content = document.querySelector( '.tab-content.active' );
				if ( current_active_tab != target && target.classList.contains( 'disabled' ) == false ) {
					current_active_tab.classList.remove( 'active' );
					target.classList.add( 'active' );
					current_active_content.classList.remove( 'active' );
					document.querySelector( ".tab-content[data-content=" + data_target + "]" ).classList.add( 'active' );
				}
			});
		});
	}

	if ( document.querySelector( 'form.add-member-form' ) ) {
		var members_interface = {
			add_form: document.querySelector( 'form.add-member-form' ),
			edit_form: document.querySelector( 'form.edit-member-form' ),
			add_inputs: document.querySelectorAll( 'form.add-member-form input' ),
			edit_inputs: document.querySelectorAll( 'form.edit-member-form input' ),
			data_check: true,
			submit_btn: document.querySelector( '#add_member_submit' ),
			edit_submit_btn: document.querySelector( '#edit_member_submit' ),
			member_edit_btns: ( document.querySelectorAll( 'table.member-table tbody i.edit' ) ) ? document.querySelectorAll( 'table.member-table tbody i.edit' ): 'none',
			member_remove_btns: ( document.querySelectorAll( 'table.member-table tbody i.remove' ) ) ? document.querySelectorAll( 'table.member-table tbody i.remove' ): 'none',
			member_table: document.querySelector( 'table.member-table' ),
			member_tbody: document.querySelector( 'table.member-table tbody' ),
			tab_list: document.querySelector( '.tab-item.member-list' ),
			tab_edit: document.querySelector( '.tab-item.edit-member' ),
			xhr: new XMLHttpRequest(),
			submit_data: function( current_form ) {
				var data = {};
				data.action = current_form + '_member_to_keeper';
				this[current_form + '_inputs'].forEach( function( input, i ) {
					var key_name = input.name;
					var value = input.value;

					if ( 'add_member_submit' !== input.id && 'edit_member_submit' !== input.id && '' !== input.name ) {
						if ( 'phone' == key_name ) {
							var cleaned = ('' + value).replace(/\D/g, '');
							var match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
							if ( match ) {
								data[key_name] = '(' + match[1] + ') ' + match[2] + '-' + match[3];
							}
						} else {
							data[key_name] = value;
						}
					}
				});
				var query_string = Object.keys( data ).map( function( key ) { return key + '=' + data[key]; } ).join('&');
				this.xhr.onreadystatechange = function() {
				    if (this.readyState == 4 && this.status == 200) {
				       // Typical action to be performed when the document is ready:
				       if ( 'undefined' != members_interface.xhr.responseText ) {
				       		var response = JSON.parse( members_interface.xhr.responseText );
				       		members_interface[current_form + '_inputs'].forEach( function( input, i ) {
				       			if ( 'add_member_submit' !== input.id && 'edit_member_submit' !== input.id && '' !== input.name && 'hidden' !== input.type ) {
				       				input.value = '';
				       			}
				       		});
				       		if ( members_interface.member_tbody ) {

					       		members_interface.member_tbody.innerHTML = '';
						        var new_member_tbody = '';

						        response.data.data.forEach( function( member, i ) {
						        	var family_id = ( 0 == member.family_id ) ? member.id: member.family_id;
									new_member_tbody += '<tr>';
									new_member_tbody += '<td>' + family_id + '</td>';
									new_member_tbody += '<td>' + member.first_name + '</td>';
									new_member_tbody += '<td>' + member.last_name + '</td>';
									new_member_tbody += '<td>' + member.phone + '</td>';
									new_member_tbody += '<td>' + member.email + '</td>';
									new_member_tbody += '<td>' + member.ministries + '</td>';
									new_member_tbody += '<td><i data-member="' + member.id + '" class="fas fa-user-edit edit"></i> <i data-member="' + member.id + '" class="fas fa-trash-alt remove"></i></td>';
									new_member_tbody += '</tr>';
						        });

						        members_interface.member_tbody.innerHTML = new_member_tbody;
						        members_interface.member_manage();
				       		} else {
				       			location.reload();
				       		}

					        if ( 'add' === current_form ) {
					        	members_interface.tab_list.click();
					        }

					        if ( 'edit' === current_form ) {
					        	members_interface.tab_list.click();
					        	members_interface.tab_edit.classList.add( 'disabled' );
					        	members_interface.tab_edit.setAttribute( 'disabled', true );
					        }
				       }

				    }
				};
				this.xhr.open("POST", ajaxurl + '?action=' + data.action, true);
				this.xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8,");
				this.xhr.send( query_string );
			},
			submit: function( e ) {
				e.preventDefault();
				var target = this;
				var current_form = target.getAttribute( 'role' );
				members_interface[ current_form + '_inputs'].forEach( function( input, i ) {
					if ( input.required && '' !== input.style && '' !== input.value && 'add_member_submit' !== input.id ) {
						input.removeAttribute( 'style' );
						input.previousElementSibling.removeAttribute( 'style' );
						members_interface.data_check = true;
					}

					if ( input.required && '' === input.value && 'add_member_submit' !== input.id && 'edit_member_submit' !== input.id ) {
						members_interface.data_check = false;
						input.style = 'border-color: red;';
						input.previousElementSibling.style = 'background: red; color: #fff;';
						input.placeholder = input.previousElementSibling.innerText.replace( '*', '' ) + ' must be completed.';
					}

					if ( members_interface.data_check && ( i + 1 ) == members_interface.add_inputs.length ) {
						members_interface.submit_data( current_form );
					}
				});
			},
			get_member_info: function( member_id ) {
				this.edit_submit_btn.addEventListener( 'click', this.submit );
				var data = {};
				data.action = 'get_member_from_keeper';
				data.member_id = member_id;
				data._wpmk_member_table = this.get_member_edit_nonce();

				var query_string = Object.keys( data ).map( function( key ) { return key + '=' + data[key]; } ).join('&');
				this.xhr.onreadystatechange = function() {
				    if (this.readyState == 4 && this.status == 200) {
				       // Typical action to be performed when the document is ready:
				       if ( 'undefined' != members_interface.xhr.responseText ) {
					       var response = JSON.parse( members_interface.xhr.responseText );
					       response = response.data.data;
					       for( var key in response ) {
					       	if ( '0000-00-00' == response[key] ) {
					       		response[key] = '';
					       	}

					       	if ( document.querySelector( 'form.edit-member-form input[name=' + key + ']' ) ) {
								document.querySelector( 'form.edit-member-form input[name=' + key + ']' ).value = response[key];
					       	}
					       }
				       }
				    }
				};
				this.xhr.open("GET", ajaxurl + '?' + query_string, true);
				this.xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8,");
				this.xhr.send();
			},
			delete_member_info: function( member_id ) {
				var data = {};
				data.action = 'delete_member_from_keeper';
				data.member_id = member_id;
				data._wpmk_member_table = this.get_member_edit_nonce();

				var query_string = Object.keys( data ).map( function( key ) { return key + '=' + data[key]; } ).join('&');
				this.xhr.onreadystatechange = function() {
				    if (this.readyState == 4 && this.status == 200) {
				       // Typical action to be performed when the document is ready:
				       var response = JSON.parse( members_interface.xhr.responseText );
				       if ( '' != response.data.data ) {
				       		members_interface.member_tbody.innerHTML = '';
					        var new_member_tbody = '';

					        response.data.data.forEach( function( member, i ) {
					        	var family_id = ( 0 == member.family_id ) ? member.id: member.family_id;
								new_member_tbody += '<tr>';
								new_member_tbody += '<td>' + family_id + '</td>';
								new_member_tbody += '<td>' + member.first_name + '</td>';
								new_member_tbody += '<td>' + member.last_name + '</td>';
								new_member_tbody += '<td>' + member.phone + '</td>';
								new_member_tbody += '<td>' + member.email + '</td>';
								new_member_tbody += '<td>' + member.ministries + '</td>';
								new_member_tbody += '<td><i data-member="' + member.id + '" class="fas fa-user-edit edit"></i> <i data-member="' + member.id + '" class="fas fa-trash-alt remove"></i></td>';
								new_member_tbody += '</tr>';
					        });

					        members_interface.member_tbody.innerHTML = new_member_tbody;
					        members_interface.member_manage();
				       } else {
				       		members_interface.member_table.innerHTML = '<span>Currently there are no members in the keeper!</span>';
				       }
				    }
				};
				this.xhr.open("POST", ajaxurl + '?action=' + data.action, true);
				this.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8,");
				this.xhr.send( query_string );
			},
			member_manage: function() {
				this.member_edit_btns = document.querySelectorAll( 'table.member-table tbody i.edit' );
				this.member_remove_btns = document.querySelectorAll( 'table.member-table tbody i.remove' );
				this.member_edit_btns.forEach( function( btn, i ) {
					btn.addEventListener( 'click', function( e ) {
						var target = this;
						var member_id = target.getAttribute( 'data-member' );
						members_interface.get_member_info( member_id );
						setTimeout( function() {
							members_interface.tab_edit.classList.remove( 'disabled' );
							members_interface.tab_edit.removeAttribute( 'disabled' );
							members_interface.tab_edit.click();
						}, 800 );
					});
				});
				this.member_remove_btns.forEach( function( btn, i ) {
					btn.addEventListener( 'click', function( e ) {
						var target = this;
						var member_id = target.getAttribute( 'data-member' );
						var confirm = window.confirm( "Are you sure you would like to delete this member?\n( WARNING THIS CANNOT BE UNDONE! )" );
						if ( confirm ) {
							members_interface.delete_member_info( member_id );
						}
					});
				});
			},
			get_member_edit_nonce: function() {
				return document.querySelector( '.member-table' ).getAttribute( 'data-security' );
			},
			init: function() {
				this.submit_btn.addEventListener( 'click', this.submit );
				if ( 'none' !== this.member_edit_btns ) {
					this.member_manage();
				}
			}
		};
		members_interface.init();
	}

})( jQuery );
