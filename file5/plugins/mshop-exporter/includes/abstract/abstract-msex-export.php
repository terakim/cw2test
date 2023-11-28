<?php
/*
=====================================================================================
                엠샵 업다운로드 / Copyright 2016 by CodeM(c)
=====================================================================================

  [ 우커머스 버전 지원 안내 ]

   워드프레스 버전 : WordPress 4.6 이상

   우커머스 버전 : WooCommerce 2.6 이상


  [ 코드엠 플러그인 라이센스 규정 ]

   (주)코드엠에서 개발된 워드프레스  플러그인을 사용하시는 분들에게는 다음 사항에 대한 동의가 있는 것으로 간주합니다.

   1. 코드엠에서 개발한 워드프레스 우커머스용 엠샵 업다운로드 플러그인의 저작권은 (주)코드엠에게 있습니다.
   
   2. 플러그인은 사용권을 구매하는 것이며, 프로그램 저작권에 대한 구매가 아닙니다.

   3. 플러그인을 구입하여 다수의 사이트에 복사하여 사용할 수 없으며, 1개의 라이센스는 1개의 사이트에만 사용할 수 있습니다. 
      이를 위반 시 지적 재산권에 대한 손해 배상 의무를 갖습니다.

   4. 플러그인은 구입 후 1년간 업데이트를 지원합니다.

   5. 플러그인은 워드프레스, 테마, 플러그인과의 호환성에 대한 책임이 없습니다.

   6. 플러그인 설치 후 버전에 관련한 운용 및 관리의 책임은 사이트 당사자에게 있습니다.

   7. 다운로드한 플러그인은 환불되지 않습니다.

=====================================================================================
*/

use PhpOffice\PhpSpreadsheet\Spreadsheet;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MSEX_Export' ) ) {

	abstract class MSEX_Export {
		private $template;

		private $slug;
		private $download_type;
		private $posts_per_page;
		private $export_all_order_items;
		private $fields = null;
		private $headers = null;
		private $spreadsheet = null;

		private $order_field_widths = array();

		function __construct( $template_id ) {
			$this->init( $template_id );
		}

		protected function init( $template_id ) {
			$this->template = get_post( $template_id );

			$this->download_type          = get_post_meta( $template_id, '_msex_download_type', true );
			$this->slug                   = get_post_meta( $template_id, '_msex_slug', true );
			$this->posts_per_page         = get_post_meta( $template_id, '_msex_posts_per_page', true );
			$this->export_all_order_items = get_post_meta( $template_id, '_msex_export_all_order_items', true );

			if ( 'excel' == $this->download_type ) {
				require_once MSEX()->plugin_path() . '/lib/excel/autoload.php';

				$this->spreadsheet = new Spreadsheet();
			}
		}

		public function get_download_type() {
			return $this->download_type;
		}

		public function get_slug() {
			return $this->slug;
		}

		public function get_posts_per_page() {
			return $this->posts_per_page;
		}

		public function is_export_all_order_items() {
			return 'yes' == $this->export_all_order_items;
		}
		public function get_fields() {
			if ( is_null( $this->fields ) ) {
				$this->fields = apply_filters( 'msex_get_fields', get_post_meta( $this->template->ID, '_msex_fields', true ), $this->slug, $this->template );
			}

			return $this->fields;
		}
		public function get_field_counts() {
			return count( $this->get_fields() );
		}
		public function get_headers() {

			if ( is_null( $this->headers ) ) {
				$headers = array();

				foreach ( $this->get_fields() as $field ) {
					$headers = array_merge( $headers, apply_filters( 'msex_get_header_' . $field['field_type'], array( $field['field_label'] ), $field ) );
				}

				$this->headers = $headers;
			}

			return $this->headers;
		}
		abstract public function get_data( $ids );
		public function setup_layout( $type ) {
			$activeSheet = $this->spreadsheet->getActiveSheet();

			// 설정 필드 총 개수
			$item_max = count( $this->get_headers() );

			//첫줄 텍스트 내용 설정
			$activeSheet->mergeCellsByColumnAndRow( 1, 1, $item_max, 1 );
			$activeSheet->getStyleByColumnAndRow( 1, 1 )->getAlignment()->setHorizontal( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );
			$activeSheet->getRowDimension( '1' )->setRowHeight( 22 );

			$activeSheet->getStyle('A1')->getFill()
			                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			                            ->getStartColor()->setARGB('FF000000');
			$activeSheet->getStyle('A1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

			$activeSheet->setCellValueByColumnAndRow( 1, 1, $this->template->post_title );
			$idx = 1;
			foreach ( $this->get_headers() as $header ) {
				$activeSheet->getStyleByColumnAndRow( $idx, 2 )->getAlignment()->setHorizontal( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );
				$activeSheet->getColumnDimensionByColumn( $idx )->setWidth( 25 );
				$activeSheet->setCellValueByColumnAndRow( $idx, 2, $header );
				$idx ++;
			}

			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx( $this->spreadsheet );
			$writer->save( MSEX_Exporter::get_tmp_file_path( $type ) );
		}
		public function outputEXCEL( $datas, $type ) {
			$this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( MSEX_Exporter::get_tmp_file_path( $type ) );

			if ( ! empty ( $this->get_fields() ) ) {
				$this->write( $datas );
			}

			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx( $this->spreadsheet );
			$writer->save( MSEX_Exporter::get_tmp_file_path( $type ) );
		}
		public function write( $order_arrays ) {
			$activeSheet = $this->spreadsheet->setActiveSheetIndex( 0 );
			$row         = $this->spreadsheet->getActiveSheet()->getHighestRow() + 1;

			foreach ( $order_arrays as $order_array ) {
				$column = 1;
				foreach ( $order_array as $value ) {
					$activeSheet->setCellValueExplicitByColumnAndRow( $column, $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING );
					$column ++;
				}
				$row ++;
			}
		}
		public function remove_emoji( $clean_text ) {
			//step #1
			$clean_text = preg_replace( '/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $clean_text );

			//step #2
			// Match Emoticons
			$regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
			$clean_text     = preg_replace( $regexEmoticons, '', $clean_text );

			// Match Miscellaneous Symbols and Pictographs
			$regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
			$clean_text   = preg_replace( $regexSymbols, '', $clean_text );

			// Match Transport And Map Symbols
			$regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
			$clean_text     = preg_replace( $regexTransport, '', $clean_text );

			// Match Miscellaneous Symbols
			$regexMisc  = '/[\x{2600}-\x{26FF}]/u';
			$clean_text = preg_replace( $regexMisc, '', $clean_text );

			// Match Dingbats
			$regexDingbats = '/[\x{2700}-\x{27BF}]/u';
			$clean_text    = preg_replace( $regexDingbats, '', $clean_text );

			return $clean_text;
		}

		protected function get_country_name( $country_code ) {
			return msex_get( WC()->countries->get_countries(), $country_code, $country_code );
		}
	}
}