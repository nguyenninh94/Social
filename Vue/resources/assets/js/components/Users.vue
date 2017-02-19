<template>
   <div id="app">
      <div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>Users</h2>
                <input type="text" @keyup="filterUsers()" name="search" class="form-control" v-model="search" placeholder="Search...">
		        </div>
		        <div class="pull-right">
				        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-users">
				           Create User
				        </button>
		        </div>
		    </div>
	  </div>
      <table class="table table-striped">
          <thead>
            <th>Id</th>
          	<th>Name</th>
          	<th>Email</th>
          	<th>Action</th>
          </thead>
          <tbody>
          	<tr v-for="u in users.data">
          	    <td>{{u.id}}</td>
          		<td>{{u.name}}</td>
          		<td>{{u.email}}</td>
          		<td>
                   <button type="submit" class="btn btn-primary" @click.prevent="editUsers(u)">Edit</button>
                   <button type="submit" class="btn btn-danger" @click.prevent="deleteUsers(u)">Remove</button>
          		</td>
          	</tr>
          </tbody>
      </table>

      <!-- Pagination -->
		<nav class="text-center">
	        <ul class="pagination">
	            <li v-if="pagination.current_page > 1">
	            	<a href="#" aria-label="Previous"
	            		@click.prevent="changePage(pagination.current_page - 1)">
	            		<span aria-hidden="true"> << </span>
	            	</a>
	            </li>
	            <li v-for="page in pagesNumber"
	                v-bind:class="[ page == isActived ? 'active' : '']">
	            	<a href="#"
	            		@click.prevent="changePage(page)">{{ page }}
	            	</a>
	            </li>
	            <li v-if="pagination.current_page < pagination.last_page">
	            	<a href="#" aria-label="Next"
	            		@click.prevent="changePage(pagination.current_page + 1)">
	            		<span aria-hidden="true"> >> </span>
	            	</a>
	            </li>
	        </ul>
	    </nav>

	    <!-- Create Item Modal -->
		<div class="modal fade" id="create-users" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Create Item</h4>
		      </div>
		      <div class="modal-body">

		      		<form method="POST" v-on:submit.prevent="createUsers">
		      			<div class="form-group">
						    <label for="name">Name:</label>
						    <input type="text" name="name" class="form-control" v-model="newUser.name">
						     <span v-if="formError['name']" class="error text-danger">{{formError['name']}}</span>
					   </div>
                       <div class="form-group">
						    <label for="email">Email:</label>
						    <input type="text" name="email" class="form-control" v-model="newUser.email">
						     <span v-if="formError['email']" class="error text-danger">{{formError['email']}}</span>
					   </div>
					   <div class="form-group">
						    <label for="password">Password:</label>
						    <input type="password" name="password" class="form-control" v-model="newUser.password">
						     <span v-if="formError['password']" class="error text-danger">{{formError['password']}}</span>
					   </div>
					   <div class="form-group">
						    <label for="password_confirmed">Confirm Password:</label>
						    <input type="password" name="password_confirmed" class="form-control"/>
					   </div>

					<div class="form-group">
						<button type="submit" class="btn btn-success">Add</button>
						<button type="reset" class="btn btn-default">Reset</button>
					</div>

		      		</form>		        
		      </div>
		    </div>
		  </div>
		</div>

		<!-- Edit Item Modal -->
		<div class="modal fade" id="edit-users" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Item</h4>
		      </div>
		      <div class="modal-body">

		      		<form method="POST" v-on:submit.prevent="updateUsers(fillUser.id)">

		      			<div class="form-group">
						<label for="name">Name:</label>
						<input type="name" name="name" class="form-control" v-model="fillUser.name"/>
						<span v-if="formErrorUpdate['name']" class="error text-danger">{{ formErrorUpdate['name'] }}</span>
					</div>
                    <div class="form-group">
						    <label for="email">Email:</label>
						    <input type="text" name="email" class="form-control" v-model="fillUser.email">
						     <span v-if="formError['email']" class="error text-danger">{{formError['email']}}</span>
					   </div>
					   <div class="form-group">
						    <label for="password">Password:</label>
						    <input type="password" name="password" class="form-control">
						     <span v-if="formError['password']" class="error text-danger">{{formError['password']}}</span>
					   </div>
					   <div class="form-group">
						    <label for="password_confirmed">Confirm Password:</label>
						    <input type="password" name="password_confirmed" class="form-control"/>
					   </div>

					<div class="form-group">
						<button type="submit" class="btn btn-success">Submit</button>
					</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		</div>



   </div>
</template>

<script>
   import { default as swal } from 'sweetalert2'

   export default {
       data() {
          return {
             users: [],
             pagination: {},
             search: '',
  	         offset: 4,
  	         formError: {},
  	         formErrorUpdate: {},
  	         newUser: {'name':'','email':'','password':''},
  	         fillUser: {'name':'','email':'','password':'','id':''}
          }
       },

       computed: {
  	  isActived: function() {
  	  	return this.pagination.current_page;
  	  },
  	  pagesNumber: function() {
  	  	if(!this.pagination.to) {
  	  		return [];
  	  	}

  	  	var from = this.pagination.current_page - this.offset;
  	  	if(from < 1) {
  	  		from = 1;
  	  	}

  	  	var to = from + (this.offset * 2);
  	  	if(to >= this.pagination.last_page) {
  	  		to = this.pagination.last_page;
  	  	}

  	  	var pagesArray = [];
  	  	while(from <= to) {
  	  		pagesArray.push(from);
  	  		from++;
  	  	}
        
        return pagesArray;
  	  }
  },

       created() {
          this.fetchUsers(this.pagination.current_page);
       },

       methods: {
           fetchUsers(page) {
              this.$http.get('api/users?page='+page).then(function(response) {
                  this.users = response.data;
                  this.pagination = response.data;
              });
           },

           filterUsers() {
             this.$http.get('api/users?search={this.search}').then(function(response) {
                  this.users = response.data;
              });
           },

           changePage(page) {
               this.pagination.curent_page = page;
               this.fetchUsers(page);
           },

           createUsers() {
              var input = this.newUser;
              this.$http.post('api/users', input).then(function(response) {
                 this.changePage(this.pagination.current_page);
                 this.newUser = {'name':'','email':'','password':''};
                 $('#create-users').modal('hide');
                 toastr.success('Created User Successfully', 'Alert Success', {timeOut:5000});
                 }, function(response) {
                    this.formError = response.data;
              });
           },

           deleteUsers(u) {
              swal({
                     title: 'Are you sure?',
                     text: "You won't be able to revert this!",
                     type: 'warning',
                     showCancelButton: true,
                     confirmButtonColor: '#3085d6',
                     cancelButtonColor: '#d33',
                     nconfirmButtonText: 'Yes, delete it!',
                     cancelButtonText: 'No, cancel!',
                     confirmButtonClass: 'btn btn-success',
                     cancelButtonClass: 'btn btn-danger',
                     buttonsStyling: false
                  }).then(function () {
                    this.$http.delete('api/users/'+u.id).then(function(response) {
                       this.changePage(this.pagination.current_page);
                     }) 
                 
                    swal(
                       'Deleted!',
                       'Your file has been deleted.',
                       'success'
                    )
                  }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                   if (dismiss === 'cancel') {
                    swal(
                      'Cancelled',
                      'Your imaginary file is safe :',
                     'error'
                   )
                  }
                })
           },

           editUsers(u) {
              this.fillUser.name = u.name;
              this.fillUser.email = u.email;
              this.fillUser.id = u.id;

              $('#edit-users').modal('show');
           },

           updateUsers(id) {
              var input = this.fillUser;
              this.$http.put('api/users/'+id, input).then(function(response) {
                 this.changePage(this.pagination.current_page);
                 this.fillUser = {'name':'','email':'','password':'','id':''};
                 $('#edit-users').modal('hide');
                 toastr.success('Updated User Successfully', 'Alert Success', {timeOut:5000});
                 }, function(response) {
                    this.formErrorUpdate = response.data;
              });
           }
       }
   }
</script>