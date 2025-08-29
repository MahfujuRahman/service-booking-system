export default [
	{
		name: "booking_date",
		label: "Enter your booking date",
		type: "date",
		value: "",
		min: new Date().toISOString().slice(0, 10),
		row_col_class: "col-md-12",
	},
];
