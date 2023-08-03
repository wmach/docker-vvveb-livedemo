-- Posts

	import(/admin/post.sql);

	
	-- overwrite get post defined in admin to get omit things like get all post descriptions

	-- get one post

	CREATE PROCEDURE get(
		IN post_id INT,
        IN slug CHAR,
        IN language_id INT,
        IN comment_count INT,
        IN comment_status INT,
        IN type CHAR,
		OUT fetch_row,
	)
	BEGIN

		SELECT _.*,pd.*,ad.admin_id,ad.username,ad.display_name,ad.email
		
		@IF isset(:comment_count)
			THEN
				,(SELECT COUNT(c.comment_id) 
						FROM comment c 
					WHERE 
						_.post_id = c.post_id
					
						@IF isset(:comment_status)
						THEN
							AND c.status = :comment_status
						END @IF
					) 
				
				AS comment_count
			END @IF 
			FROM post AS _
			LEFT JOIN post_content pd ON (_.post_id = pd.post_id AND pd.language_id = :language_id)  
			LEFT JOIN admin ad ON (_.admin_id = ad.admin_id)  
		WHERE 1 = 1

            @IF isset(:slug)
			THEN 
				AND pd.slug = :slug 
        	END @IF			

            @IF isset(:post_id)
			THEN
                AND _.post_id = :post_id
        	END @IF			
			
			@IF isset(:type)
			THEN
                AND _.type = :type
        	END @IF			

        LIMIT 1; 
		
		
		SELECT `key` as array_key,`value` as array_value FROM post_meta as _
			WHERE _.post_id = @result.post_id
		
          
	END


	-- Get categories

	CREATE PROCEDURE postCategories(
		IN  id_post INT,
	)
	BEGIN
	END
