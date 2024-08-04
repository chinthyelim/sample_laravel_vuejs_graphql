<template>
	<v-layout row wrap align-center justify-center class="text-xs-center">
		<v-container fluid>
			<div flex-row full-width>
				<div class="page-message">
					<v-alert
						v-show="messageStore.message"
						:type="messageStore.messageType"
						dismissible
						>{{ messageStore.message }}</v-alert
					>
				</div>
				<v-card>
					<v-card-text class="pt-5">
						<!-- breadcrumbs line -->
						<div class="page-breadcrumbs">
							<v-breadcrumbs :items="breadcrumbs"> </v-breadcrumbs>

							<!-- close button area -->
							<div class="page-title-button q-gutter-xs">
								<slot name="title-buttons"></slot>
							</div>
						</div>

						<!-- title -->
						<div class="page-title">
							<h1>{{ props.title }}</h1>
							<div class="page-title-button q-gutter-xs"></div>
						</div>

						<!-- default -->
						<slot></slot>
					</v-card-text>
				</v-card>
			</div>
			<common-dialog
				v-model="deleteControl.dlgDeletePrompt.show"
				:message="deleteControl.dlgDeletePrompt.message"
				:type="deleteControl.dlgDeletePrompt.type"
				@ok="confirmDelete"
				@cancel="cancel"
			></common-dialog>
		</v-container>
	</v-layout>
</template>

<script lang="ts">
export default { name: "CommonPage" };
</script>

<script setup lang="ts">
import { ref, withDefaults } from "vue";
import CommonDialog, { DialogCtl } from "./CommonDialog.vue";
import { useMessageStore } from "../../stores/MessageStore";

// props
const props = withDefaults(
	defineProps<{
		title: string;
		breadcrumbs: Array<any>;
		closeButton?: boolean;
	}>(),
	{
		title: "",
		breadcrumbs: () => [],
		closeButton: true,
	}
);

// emits
const emit = defineEmits<{
	(e: "cancel"): void;
	(e: "delete", value: number): void;
}>();

const messageStore = useMessageStore();
const deleteControl = ref({
	dlgDeletePrompt: {
		show: false,
		message: "",
		type: "ConfirmCancel",
	} as DialogCtl,
	selectedItem: { id: 0, name: "" },
});

const promptConfirmDelete = (item: any) => {
	deleteControl.value.selectedItem.id = item.id;
	deleteControl.value.selectedItem.name = item.name;
	deleteControl.value.dlgDeletePrompt.message = `Confirm delete selected ${deleteControl.value.selectedItem.name}?`;
	deleteControl.value.dlgDeletePrompt.show = true;
};

const confirmDelete = () => {
	deleteControl.value.dlgDeletePrompt.show = false;
	emit("delete", deleteControl.value.selectedItem.id);
};

const cancel = () => {
	deleteControl.value.dlgDeletePrompt.show = false;
};

defineExpose({
	promptConfirmDelete,
});
</script>

<style lang="scss">
$page-title-top: 20px;

.page-message {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	z-index: 9999;
}

.page-breadcrumbs {
	position: relative;
}

.page-title {
	position: relative;
	padding-bottom: 40px;
}

.page-title-button {
	position: absolute;
	right: 10px;
	top: $page-title-top - 12px;
}
</style>
