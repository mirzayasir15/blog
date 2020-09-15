<div id="site_content">
    <div id="content" style="width: 100%; padding-right: 25px;">
        <!-- insert the page content here -->
        <h1>All Users</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hovered">
                <tr>
                    <th>#</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php if(count($users) > 0) { $index=1; foreach($users as $user) { ?>
                    <tr>
                        <td><?= $index++; ?></td>
                        <td><?= $user->email; ?></td>
                        <td><?= $user->username; ?></td>
                        <td><?= $user->user_type; ?></td>
                        <td>Status</td>
                        <td style="width: 120px;">
                            <select class="form-control">
                                <option value='enable'>Enable</option>
                                <option value='disable'>Disable</option>
                            </select>
                        </td>
                    </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="6"><span class='text-danger'>No record found.</span></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>