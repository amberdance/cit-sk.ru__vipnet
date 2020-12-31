<template>
  <el-dialog width="40%" :visible.sync="isShowed" :title="title" @close="hide">
    <el-table :data="notes" :highlight-current-row="false">
      <el-table-column label="создана" prop="created" />

      <el-table-column label="заметка" prop="note" width="300">
        <template #default="{row}">
          <div v-if="row.id == editingRow.id">
            <el-input
              v-model="editingRow.note"
              type="textarea"
              rows="5"
            ></el-input>

            <el-button-group style="margin:3px 0; float:right;">
              <el-button
                size="mini"
                icon="el-icon-close"
                @click="editingRow.id = null"
              ></el-button>

              <el-button
                size="mini"
                icon="el-icon-check"
                @click="edit"
              ></el-button>
            </el-button-group>
          </div>

          <div v-else>{{ row.note }}</div>
        </template>
      </el-table-column>

      <el-table-column>
        <template #default="row">
          <el-button
            size="mini"
            icon="el-icon-edit"
            @click="activateEditableInput(row.$index, row.row)"
          >
          </el-button>
          <el-button
            size="mini"
            type="danger"
            icon="el-icon-delete"
            @click="remove(row.$index, row.row.id)"
          >
          </el-button>
        </template>
      </el-table-column>
    </el-table>
  </el-dialog>
</template>

<script>
export default {
  data() {
    return {
      isShowed: false,
      title: null,
      notes: [],

      editingRow: {
        id: null,
        index: null,
        note: null
      }
    };
  },

  methods: {
    show({ label, notes }) {
      this.title = label;
      this.notes = notes;
      this.isShowed = true;
    },

    async remove(index, id) {
      await this.$HTTPPost({
        route: "/reference/remove-note",
        payload: {
          id
        }
      });

      this.notes.splice(index, 1);

      if (!this.notes.length) this.hide();
    },

    async edit() {
      await this.$HTTPPost({
        route: "/reference/update-note",
        payload: {
          id: this.editingRow.id,
          note: this.editingRow.note
        }
      });

      this.notes[this.editingRow.index].note = this.editingRow.note;
      this.editingRow.id = null;
      this.editingRow.index = null;
      this.editingRow.note = null;
    },

    activateEditableInput(index, row) {
      this.editingRow.id = row.id;
      this.editingRow.index = index;
      this.editingRow.note = row.note;
    },

    hide() {
      this.isShowed = false;
    }
  }
};
</script>
