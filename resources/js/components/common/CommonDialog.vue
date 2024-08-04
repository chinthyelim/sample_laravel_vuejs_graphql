<template>
	<v-dialog v-model="modelValue" persistent width="40vw">
		<v-card>
			<v-card-title>
				{{ message }}
			</v-card-title>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn v-if="showConfirm" @click="ok" color="indigo-darken-3"
					>Confirm</v-btn
				>
				<v-btn v-if="showOK" @click="ok" color="indigo-darken-3">OK</v-btn>
				<v-btn v-if="showCancel" @click="cancel">Cancel</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script lang="ts">
export default { name: "CommonDialog" };
</script>

<script setup lang="ts">
import { ref, computed, watch } from "vue";

export interface DialogCtl {
	show?: boolean;
	message: string;
	type: "OKOnly" | "OKCancel" | "ConfirmCancel";
}

const props = withDefaults(defineProps<DialogCtl>(), {
	modelValue: false,
	message: "",
	type: "OKOnly",
});

const emit = defineEmits<{
	(e: "cancel"): void;
	(e: "ok"): void;
}>();

const modelValue = ref(false);
const message = ref("");

const showCancel = computed(
	() => props.type == "OKCancel" || props.type == "ConfirmCancel"
);
const showOK = computed(
	() => props.type == "OKOnly" || props.type == "OKCancel"
);
const showConfirm = computed(() => props.type == "ConfirmCancel");

const ok = () => {
	emit("ok");
};

const cancel = () => {
	emit("cancel");
};

watch(
	props,
	() => {
		message.value = props.message;
		modelValue.value = props.modelValue;
	},
	{ immediate: true }
);
</script>
