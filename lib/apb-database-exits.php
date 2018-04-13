<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AWE Database Autoload
 *
 * @class 		database_exits
 * @version		1.0
 * @package		AweBooking/Classes/
 * @author 		AweTeam
 */


class database_exits {

	public function __construct() {
		$this->check_table();
	}

	public function check_table() {
		global $wpdb;
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		/**
		 * Status 0: completed
		 * Status 1: not available
		 * Status 2: Available
		 * Status 3: Pending.
		 */
		if ( ! $this->table_exists( 'apb_availability' ) ) {
			$simple_sql = "CREATE TABLE {$wpdb->prefix}apb_availability (
						unit_id int(10) UNSIGNED NOT NULL COMMENT 'Identifier for a room.',
						year int(11) NOT NULL DEFAULT 0 COMMENT 'The calendar year for which this availability row is relevant',
						month int(11) NOT NULL DEFAULT 0 COMMENT 'The month for which this availability row is relevant',
						d1 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 1',
						d2 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 2',
						d3 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 3',
						d4 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 4',
						d5 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 5',
						d6 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 6',
						d7 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 7',
						d8 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 8',
						d9 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 9',
						d10 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 10',
						d11 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 11',
						d12 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 12',
						d13 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 13',
						d14 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 14',
						d15 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 15',
						d16 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 16',
						d17 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 17',
						d18 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 18',
						d19 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 19',
						d20 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 20',
						d21 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 21',
						d22 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 22',
						d23 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 23',
						d24 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 24',
						d25 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 25',
						d26 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 26',
						d27 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 27',
						d28 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 28',
						d29 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 29',
						d30 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 30',
						d31 int(11) NOT NULL DEFAULT 2 COMMENT 'Month Day 31',
						INDEX month (month),
						UNIQUE INDEX month_key (unit_id, year, month),
						INDEX unit_id (unit_id),
						INDEX year (year)
					) {$charset_collate};";
			 $wpdb->query( $simple_sql );
		}
		if ( ! $this->table_exists( 'apb_booking_options' ) ) {
			$simple_sql = "CREATE TABLE {$wpdb->prefix}apb_booking_options (
							id int(11) NOT NULL AUTO_INCREMENT,
							entity_type varchar(128) NOT NULL DEFAULT '' COMMENT 'The entity type this data is attached to',
							object_name varchar(128) NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
							object_id int(10) UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
							revision_id int(10) UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
							total int(10) UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
							option_name varchar(255) NOT NULL,
							option_quantity int(11) DEFAULT NULL,
							option_operation varchar(255) NOT NULL,
							option_value float DEFAULT NULL,
							option_icon varchar(128) DEFAULT NULL,
							option_desc text DEFAULT NULL,
							status varchar(20) NOT NULL,
							PRIMARY KEY (id),
							INDEX bundle (object_name),
							INDEX entity_id (object_id),
							INDEX entity_type (entity_type),
							INDEX revision_id (revision_id)
					) {$charset_collate};";
			$wpdb->query( $simple_sql );

		}
		 $apb_booking_options = $this->column_exits( "{$wpdb->prefix}apb_booking_options", 'option_icon');

		if( !in_array('option_icon', $apb_booking_options ) ){ 
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}apb_booking_options ADD option_icon varchar(128) DEFAULT NULL AFTER option_desc" );
		}
		 

		if ( ! $this->table_exists( 'apb_pricing' ) ) {
			$simple_sql = "CREATE TABLE {$wpdb->prefix}apb_pricing (
							unit_id int(10) UNSIGNED NOT NULL COMMENT 'Identifier for a booking unit.',
							year int(11) NOT NULL DEFAULT 0 COMMENT 'The calendar year for which this pricing row is relevant',
							month int(11) NOT NULL DEFAULT 0 COMMENT 'The month for which this pricing row is relevant',
							d1 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 1',
							d2 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 2',
							d3 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 3',
							d4 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 4',
							d5 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 5',
							d6 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 6',
							d7 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 7',
							d8 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 8',
							d9 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 9',
							d10 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 10',
							d11 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 11',
							d12 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 12',
							d13 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 13',
							d14 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 14',
							d15 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 15',
							d16 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 16',
							d17 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 17',
							d18 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 18',
							d19 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 19',
							d20 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 20',
							d21 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 21',
							d22 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 22',
							d23 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 23',
							d24 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 24',
							d25 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 25',
							d26 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 26',
							d27 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 27',
							d28 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 28',
							d29 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 29',
							d30 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 30',
							d31 VARCHAR(20) NOT NULL DEFAULT '0' COMMENT 'Month Day 31',
							INDEX month (month),
							UNIQUE INDEX month_key (unit_id, year, month),
							INDEX unit_id (unit_id),
							INDEX year (year)
					) {$charset_collate};";
			 $wpdb->query( $simple_sql );
		}
	}

	public function table_exists( $tablename ) {
		global $wpdb;
		if ( strpos( $tablename, $wpdb->prefix ) === false ) {
			$tablename = $wpdb->prefix . $tablename;
		}
		$sql_check_table = $wpdb->prepare(
			"SELECT COUNT(*) AS count
			FROM information_schema.tables
			WHERE table_schema = '" . DB_NAME . "'
			AND table_name = %s ",
			$tablename
		);

		$res = $wpdb->get_results( $sql_check_table );
		return $res[0]->count;
	}
	public function column_exits( $table, $column_name ){
		global $wpdb;
		$list =  $wpdb->get_results("	
			SELECT `COLUMN_NAME` 
			FROM `INFORMATION_SCHEMA`.`COLUMNS` 
			WHERE `TABLE_SCHEMA`='{$wpdb->dbname}' 
			    AND `TABLE_NAME`='wp_apb_booking_options';
			");
		$args = array();
		foreach ($list  as $key => $value) {
			$args[] = $value->COLUMN_NAME;
		}

		return $args;
	}
}
