import isEmail from "validator/es/lib/isEmail";
import isLength from "validator/es/lib/isLength";

const required =
	(msg = "") =>
	(v: string | number) =>
		!!v.toString() || msg || "Field is required";

const maxLength =
	(max: number, msg = "") =>
	(v: string) =>
		isLength(v, { max }) ||
		msg ||
		`Length must be smaller or equal than ${max}.`;

const email =
	(msg = "") =>
	(v: string) =>
		!v.toString() || isEmail(v) || msg || "Must be a valid email address";

const requiredAndMaxlength =
	(max: number, msg = "") =>
	(v: string) =>
		(!!v.toString() && isLength(v, { max })) ||
		msg ||
		`Field is required  and length must be smaller or equal than ${max}.`;

const requiredId =
	(msg = "") =>
	(v: string | number) =>
		(!!v.toString() && v.toString() != "0") || msg || "Field is required";

const phoneNumber =
	(msg = "") =>
	(v: string) =>
		!v.toString() ||
		(!!v.toString() &&
			new RegExp(
				"^[\\+]?[ 0-9]{0,1}[ ]{0,1}[.(-]?[0-9]{3}[)]?[-\\s\\.]?[0-9]{3}[-\\s\\.]?[0-9]{4,6}$"
			).test(v)) ||
		msg ||
		"Must be a phone number";

const noWhiteSpace =
	(msg = "") =>
	(v: string) =>
		!v.toString() ||
		(!!v.toString() && !new RegExp("\\s").test(v)) ||
		msg ||
		"Must be no white space char";

export {
	required,
	maxLength,
	email,
	requiredAndMaxlength,
	requiredId,
	phoneNumber,
	noWhiteSpace,
};
