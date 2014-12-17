<?php

if (!defined('VALID_REQUEST')) {
	http_response_code(404);
	exit;
}

class InventoryController {
	/**
	 * Makes an API call to add a book to the database.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function addNewBook($request, &$jsonResult) {
		global $DB;

		$book = $request['book'];

		$query = "
			INSERT INTO Book
				(`ISBN`,
					`Title`,
					`SalePrice`,
					`PageCount`,
					`Edition`,
					`Language`,
					`Publisher`)
			VALUES
				(:isbn,
					:title,
					:salePrice,
					:pageCount,
					:edition,
					:language,
					:publisher);
		";
		$insertion = $DB->query($query, array(
				'isbn' => $book['isbn'],
				'title' => $book['title'],
				'salePrice' => $book['salePrice'],
				'pageCount' => $book['pageCount'],
				'edition' => $book['edition'],
				'language' => $book['language'],
				'publisher' => $book['publisher'],
			));

		$jsonResult['success'] = true;
	}

	/**
	 * Makes an API call to update a book's information in the database.
	 * 
	 * @param array $request A bundle of request data. Usually comes from URL parameter string.
	 * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
	 */
	public function updateBook($request, &$jsonResult) {
		global $DB;

		$book = $request['book'];

		$query = "
			UPDATE Book
			SET
				Title = :title,
				SalePrice = :salePrice,
				PageCount = :pageCount,
				Edition = :edition,
				Language = :language,
				Publisher = :publisher
			WHERE
				ISBN = :isbn;
		";
		$insertion = $DB->query($query, array(
				'isbn' => $book['isbn'],
				'title' => $book['title'],
				'salePrice' => $book['salePrice'],
				'pageCount' => $book['pageCount'],
				'edition' => $book['edition'],
				'language' => $book['language'],
				'publisher' => $book['publisher'],
			));

		$jsonResult['success'] = true;
	}
    
    /**
     * Makes an API call to update a book copy as part of checking in/out a book by the user.
     * 
     * @param array $request A bundle of request data. Usually comes from URL parameter string.
     * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
     */
    public function updateBookCopy($request, &$jsonResult) {
		global $DB;

        $bookCopy = $request['bookCopy'];
        
        $query = "
			UPDATE BookCopy
			SET
				IsForSale = :isForSale,
				HeldBy = :heldBy,
				ISBN = :isbn
			WHERE
				BookCopyId = :bookCopyId;
		"; 
        $insertion = $DB->query($query, array(
            'isForSale' => $bookCopy['isForSale'],
            'heldBy' => $bookCopy['heldBy'],
            'isbn' => $bookCopy['isbn'],
            'bookCopyId' => $bookCopy['bookCopyId'],
        ));

		$jsonResult['success'] = true;
    }
    
    /**
     * Makes an API call to add a book transaction to the bookTransaction table as part of record keeping
     * 
     * @param array $request A bundle of request data. Usually comes from URL parameter string.
     * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
     */
    public function addBookTransaction($request, &$jsonResult) {
		global $DB;

        $bookTrans = $request['bookTrans'];
        
        //omit BookTransactionID since it's auto-increment
        $query = "
	        INSERT INTO BookTransaction 
				(`BookCopyId`,
					`Time`,
					`ExpectedReturn`,
					`ActualReturn`,
					`CardNumber`)
			VALUES
				(:bookCopyId,
					:transDate,
					:expectDate,
					:actualDate,
					:cardNumber);
		";
        $transKey = $DB->query($query, array(
            'bookCopyId' => $bookTrans['bookCopyId'],
            'transDate' => $bookTrans['transDate'],
            'expectDate' => $bookTrans['expectDate'],
            'actualDate' => $bookTrans['actualDate'],
            'cardNumber' => $bookTrans['cardNumber'],
        ));

        
		$jsonResult['success'] = true;
        $jsonResult['data'][] = $DB->lastInsertedId();
        
    }
    
    /**
     * Makes an API call to add a book transaction to the bookTransaction table as part of record keeping
     * 
     * @param array $request A bundle of request data. Usually comes from URL parameter string.
     * @param array $jsonResult A bundle that holds the JSON result. Requires success element to be true or false.
     */
    public function updateReturnTransaction($request, &$jsonResult) {
		global $DB;

        $bookReturn = $request['returnTrans'];
        
        //omit BookTransactionID since it's auto-increment
        $query = "
			UPDATE BookTransaction
			SET
				ActualReturn = :returnDate
			WHERE
				BookTransactionId = :bookTransactionId;
		"; 
        $transKey = $DB->query($query, array(
            'returnDate' => $bookReturn['returnDate'],
            'bookTransactionId' => $bookReturn['bookTransactionId'],
        ));

		$jsonResult['success'] = true;
        
    }
}

?>
