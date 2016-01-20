<?php
/**
 * Copyright (c) 2015 Cleber Santos
 *
 * Written by Cleber Santos <oclebersantos@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * Public License can be found at http://www.gnu.org/copyleft/gpl.html
 *
 * Function Name: ShortCode Menu Página
 * Function URI: http://culturadigital.br
 * Description: Cria um menu com a url das páginas de todos os filhos da página mãe
 * Author: Cleber Santos
 * Author URI: http://culturadigital.br/membros/clebersantos
 * Version: 0.1
 */

class MenuParentPag
{
  // ATRIBUTES ////////////////////////////////////////////////////////////////////////////////////

  // METHODS //////////////////////////////////////////////////////////////////////////////////////
 
	/**
	 * 
	 *
	 * @name    menu_pags
	 * @author  Cleber Santos <oclebersantos@gmail.com>
	 * @since   2015-05-12
	 * @updated 2015-05-12
	 * @return  string
	 */
	function menu_pags( $atts ) {

		global $post;

		$current_page_id = get_the_ID();

		$parent = $post->post_parent;

		if( $parent == 0 )
			$parent = $current_page_id;
			
		$pages = get_pages( array( 'parent' => $parent, 'sort_column' => 'title', 'sort_order' => 'asc', 'number' => '6' ) );


    	$output = "<ul id='menu-abas' class='itens'>";
			
			foreach( $pages as $page ) {	
				
				$class = null;

				if ( $page->ID == $current_page_id )
					$class = "current";
			
			 	$output .= "<li class='item " . $class . "'><a href='"  . get_page_link( $page->ID ) . "'>" . $page->post_title .  "</a></li>";  

			}

		$output .=	"</ul>";

		return $output;
	}


  // CONSTRUCTOR //////////////////////////////////////////////////////////////////////////////////
  /**
	 * construtor
	 *
	 * @name    
	 * @author  Cleber Santos <cleber.santos@cultura.gov.br>
	 * @since   2012-06-01
	 * @updated 2012-06-01
	 * @return  string
	 */
  function __construct()
  {
	// add menu parent pagshortcode
	add_shortcode( 'menu_parent_pag', array( &$this, 'menu_pags' ) );
  }

  // DESTRUCTOR ///////////////////////////////////////////////////////////////////////////////////

}

$MenuParentPag = new MenuParentPag();

?>
