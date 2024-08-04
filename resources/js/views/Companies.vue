<template>
	<common-page
		v-show="ctrl == 'list'"
		ref="CommonPageRef"
		title="Companies"
		:breadcrumbs="['Companies']"
		@delete="confirmDelete"
	>
		<!-- title button -->
		<template #title-buttons>
			<v-tooltip location="bottom">
				<template v-slot:activator="{ props }">
					<v-btn
						v-bind="props"
						@click="refreshList('')"
						outlined
						dark
						color="primary"
						icon="refresh"
						class="mr-2"
						>R<!--<v-icon v-bind="props" icon="mdi-refresh"></v-icon>-->
					</v-btn>
				</template>
				<span>Refresh</span>
			</v-tooltip>
			<v-tooltip location="bottom">
				<template v-slot:activator="{ props }">
					<v-btn
						v-bind="props"
						@click="addNew"
						outlined
						dark
						color="primary"
						icon="mdi-add"
						>+<!--<v-icon v-bind="props" icon="mdi-add"></v-icon>-->
					</v-btn>
				</template>
				<span>Add New</span>
			</v-tooltip>
		</template>

		<!-- Table -->
		<v-card>
			<v-card-text>
				<EasyDataTable
					v-model:server-options="table.serverOptions"
					:server-items-length="table.serverItemsLength"
					:loading="table.loading"
					:headers="table.headers"
					:items="table.items"
					hide-footer
				>
					<template #item-logo="item">
						<img :src="siteUrl + 'storage/' + item.logo" width="50" />
					</template>
					<template #item-operation="item">
						<div class="operation-wrapper">
							<v-btn
								@click="edit(item)"
								outlined
								dark
								color="teal lighten-4"
								icon="mdi-edit"
								class="mr-2"
								>E<!--<v-icon>edit</v-icon>-->
							</v-btn>
							<v-btn
								@click="promptConfirmDelete(item)"
								outlined
								dark
								color="red lighten-4"
								icon="mdi-delete"
								>D<!--<v-icon>delete</v-icon>-->
							</v-btn>
						</div>
					</template>
				</EasyDataTable>
			</v-card-text>
			<v-card-actions>
				<div class="ml-3">
					Page {{ table.serverOptions.page + " of " + table.serverItemsLength }}
				</div>
				<v-spacer></v-spacer>
				<v-btn
					@click="prevPage"
					outlined
					dark
					color="indigo-darken-3"
					:disabled="table.serverOptions.page <= 1"
					>Previous Page</v-btn
				>
				<v-btn
					@click="nextPage"
					outlined
					dark
					color="indigo-darken-3"
					:disabled="table.serverOptions.page > table.serverItemsLength - 1"
					>Next Page</v-btn
				>
			</v-card-actions>
		</v-card>
	</common-page>
	<company-details-page
		v-if="ctrl == 'details'"
		:mode="mode"
		:models="models"
		:siteUrl="siteUrl"
		@created="refreshList('New company has been successfully added!')"
		@updated="updateSelectedRow"
		@cancel="ctrl = 'list'"
	></company-details-page>
</template>

<script lang="ts">
export default { name: "CompaniesPage" };
</script>

<script setup lang="ts">
import { Ref, ref } from "vue";
import { adminApi } from "../axios";
import type { Company } from "../api-client/api";
import type { Header, Item, ServerOptions } from "vue3-easy-data-table";
import CommonPage from "../components/common/CommonPage.vue";
import CompanyDetailsPage from "../components/CompanyDetailsPage.vue";
import { useMessageStore } from "../stores/MessageStore";

defineProps<{
	siteUrl: string;
}>();

const messageStore = useMessageStore();

const table = ref({
	headers: [
		{ text: "Logo", value: "logo", width: 75, sortable: true },
		{ text: "Name", value: "name", sortable: true },
		{ text: "Email", value: "email", sortable: true },
		{ text: "Website", value: "website", sortable: true },
		{ text: "Operation", value: "operation", fixed: true, width: 55 },
	] as Header[],
	items: [] as Item[] as Company[],
	loading: false,
	serverItemsLength: 0,
	serverOptions: {
		page: 1,
		rowsPerPage: 10,
		sortBy: "id",
		sortType: "desc",
	} as ServerOptions,
});
const CommonPageRef = ref<InstanceType<typeof CommonPage>>();
const ctrl = ref("list");
const mode = ref("");
const models: Ref<Company> = ref({
	id: 0,
	name: "",
	email: "",
	logo: "imgNoImage.png",
	website: "",
} as Company);

const prevPage = () => {
	table.value.serverOptions.page--;
	refreshList("");
};

const nextPage = () => {
	table.value.serverOptions.page++;
	refreshList("");
};

const refreshList = async (successMsg = "") => {
	if (successMsg) {
		messageStore.success(successMsg);
	}
	ctrl.value = "list";
	table.value.loading = true;
	try {
		const returnData =
			(
				await adminApi.getCompanies({
					current_page_number: table.value.serverOptions.page,
					rows_per_page: table.value.serverOptions.rowsPerPage,
				})
			).data || ([] as Company[]);
		table.value.items = returnData.data;
		table.value.serverItemsLength = Math.ceil(
			returnData.total_rows / table.value.serverOptions.rowsPerPage
		);
		if (table.value.serverOptions.page == 0 && table.value.serverItemsLength) {
			table.value.serverOptions.page = 1;
		} else if (!table.value.serverItemsLength) {
			table.value.serverOptions.page = 0;
		}
	} catch (error: any) {
		messageStore.error(error);
	} finally {
		table.value.loading = false;
	}
};

const addNew = () => {
	models.value = {
		id: 0,
		name: "",
		email: "",
		logo: "imgNoImage.png",
		website: "",
	} as Company;
	mode.value = "create";
	ctrl.value = "details";
};

const edit = (item: any) => {
	mode.value = "update";
	ctrl.value = "details";
	const { key, ...params } = item;
	models.value = params;
};

const updateSelectedRow = (updated: Company) => {
	messageStore.success("Selected company has been successfully updated!");
	ctrl.value = "list";
	const index = table.value.items.findIndex((item) => item.id === updated.id);
	table.value.items.splice(index, 1, updated);
};

const promptConfirmDelete = (row: any) => {
	CommonPageRef.value?.promptConfirmDelete({ id: row.id, name: row.name });
};

const confirmDelete = async (id: number) => {
	try {
		messageStore.message = "";
		await adminApi.deleteCompany({ id });
		if (table.value.items.length == 1) {
			table.value.serverOptions.page = table.value.serverItemsLength - 1;
		}
		refreshList("Selected company has been successfully deleted!");
	} catch (error: any) {
		messageStore.error(error);
	}
};

refreshList("");
</script>
