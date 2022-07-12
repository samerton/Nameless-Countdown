{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Toplbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$COUNTDOWN}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$COUNTDOWN}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form action="" method="post">
                            <div class="form-group">
                                <label for="name">{$COUNTDOWN_NAME}</label>
                                <input type="text" id="name" name="name" value="{$COUNTDOWN_NAME_VALUE}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="description">{$COUNTDOWN_DESCRIPTION}</label>
                                <textarea id="description" name="description" class="form-control">{$COUNTDOWN_DESCRIPTION_VALUE}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="expires">{$COUNTDOWN_EXPIRES}</label>
                                <input type="datetime-local" id="expires" name="expires" value="{$COUNTDOWN_EXPIRES_VALUE}" min="{$COUNTDOWN_EXPIRES_MIN}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}" />
                            </div>
                            <div class="form-group">
                                <button type="button" onclick="toggleRemoveModal()" class="btn btn-danger">{$REMOVE_COUNTDOWN}</button>
                            </div>
                        </form>

                    </div>
                </div>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- Remove countdown modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$REMOVE_COUNTDOWN_CONFIRM}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$REMOVE_COUNTDOWN_CONFIRM_NO}</button>
                    <button type="button" onclick="removeCountdown()" class="btn btn-primary">{$REMOVE_COUNTDOWN_CONFIRM_YES}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

<script type="text/javascript">
    function toggleRemoveModal() {
      $('#removeModal').modal().show();
    }

    function removeCountdown() {
      const remove = $.post("{$REMOVE_COUNTDOWN_ACTION}", { token: "{$TOKEN}" });
      remove.done(function () { window.location.reload(); })
    }
</script>

</body>

</html>