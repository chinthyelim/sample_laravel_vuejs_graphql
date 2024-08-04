import { computed } from "vue";

interface FormOptions {
	mode: "create" | "update";
	enabledEdit: boolean;
}

export function useFormControl(form: FormOptions) {
	const enabled = computed(() => form.enabledEdit || form.mode == "create");

	const bgColor = computed(() => {
		if (form.enabledEdit) {
			return "green-lighten-5";
		} else {
			return "light-blue-lighten-5";
		}
	});

	return { enabled, bgColor };
}
