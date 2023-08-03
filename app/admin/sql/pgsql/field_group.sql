-- Taxonomy

	-- get all taxonomies

	CREATE PROCEDURE getAll(
		IN field_group_item_id INT,
		
		-- pagination
		IN start INT,
		IN limit INT,
		
		-- filter
		IN post_type CHAR,

		OUT fetch_all, 
		OUT fetch_one 
	)
	BEGIN
		-- field_group_item
		SELECT *, field_group_id as array_key 
			FROM field_group as field_group 
				-- LEFT JOIN field_group_to_site t2s ON (field_group_item.field_group_item_id = t2s.field_group_item_id) 
			
			WHERE 1 = 1
			
			@IF isset(:post_type)
			THEN 
			
				AND post_type = :post_type
				
			END @IF			
			
			@IF isset(:limit)
			THEN
				LIMIT :start, :limit
			END @IF;
		
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(field_group.field_group_id, field_group) -- this takes previous query removes limit and replaces select columns with parameter product_id
			
		) as count;		
			
	END
	

