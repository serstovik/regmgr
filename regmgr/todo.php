	Review and understand existing code.
	Think about backend index configuration.
	Finish code comments and method headers.
	Update author in comments for classes and methods headers.
	Review and think on status logic code.
	Implement files upload method in rmApplication model.

	Complete backend index with real DB data.
	Implement form loader and status for validations output.


rmCfg

	- getCfg(); 	// Returns entire config
	- getParam($root, $branch = '', $param = ''); // Returns specified chunk of config
	- getTypes($type = '', $param = ''); // Returns list of types or specified parameter of exact type
	- getStatuses($status = ''); // Returns list of statuses or title for exact status
	- getEmails($event = '') = array(); // Returns list of all registered events, or list of emails for exact event

	getTypes();
	getTypes('catz');
	getTypes('catz', 'template');

	getEmails(); // ['status.new' => [0 => ..., 1 => ...], 'status.open' => [...] ]
	getEmails('status.new'); // [0 => ['template' => ....], 1 => ...]

test github with morweb team