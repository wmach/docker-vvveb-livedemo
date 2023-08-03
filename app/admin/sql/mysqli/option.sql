-- Taxonomy

	-- get all taxonomies

	CREATE PROCEDURE getAll(
		-- pagination
		IN start INT,
		IN limit INT,
		
		-- filter
		IN type CHAR,

		OUT fetch_all, 
		OUT fetch_one 
	)
	BEGIN
		-- option_item
		SELECT *, option_id as array_key 
			FROM option as option 
				-- LEFT JOIN option_to_site t2s ON (option_item.option_item_id = t2s.option_item_id) 
			
			WHERE 1 = 1
			
			@IF isset(:post_type)
			THEN 
			
				AND post_type = :post_type
			END @IF				
							
			@IF isset(:type)
			THEN 
			
				AND type = :type
				
			END @IF			
			
			@IF isset(:limit)
			THEN
				LIMIT :start, :limit
			END @IF;
		
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(option.option_id, option) -- this takes previous query removes limit and replaces select columns with parameter product_id
			
		) as count;		
			
	END
	

	-- get option

	PROCEDURE get(
		IN option_id INT,
		OUT fetch_row, 
	)
	BEGIN
		-- option
		SELECT *
			FROM `option` as _ WHERE option_id = :option_id;
	END
	
	-- add option

	PROCEDURE add(
		IN option ARRAY,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		:option_data  = @FILTER(:option, option);
		
		
		INSERT INTO `option` 
			
			( @KEYS(:option_data) )
			
	  	VALUES ( :option_data);

	END
	
	-- edit option
	CREATE PROCEDURE edit(
		IN option ARRAY,
		IN option_id INT,
		OUT affected_rows
	)
	BEGIN

		-- allow only table fields and set defaults for missing values
		@FILTER(:option, option);

		UPDATE `option`
			
			SET @LIST(:option) 
			
		WHERE option_id = :option_id


	END
	
	-- delete option

	PROCEDURE delete(
		IN option_id ARRAY,
		OUT affected_rows, 
	)
	BEGIN
		-- option
		DELETE FROM `option` WHERE option_id IN (:option_id);
	END
