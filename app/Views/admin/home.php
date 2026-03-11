<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <div class="border-bottom border-1 mb-4 pb-4 d-flex align-items-center justify-content-between gap-3">
                <h2 class="mb-0">Admin Home</h2>
                <div class="btn-group" role="group" aria-label="Page actions">
                    <button type="button" class="btn btn-outline-primary"><i class="bi bi-plus-circle-fill"></i><span class="d-none d-lg-inline"> New</span></button>
                    <button type="button" class="btn btn-outline-primary" id="btn-datatable-refresh"><i class="bi bi-arrow-clockwise"></i><span class="d-none d-lg-inline"> Refresh</span></button>
                </div>
            </div>

            <p>This is an example table with some sample data.</p>

            <div class="table-responsive">
                <table id="example-table" class="table table-bordered table-striped table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>1</td><td>Alice</td><td>Anderson</td><td>alice.anderson@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2023-01-15</td></tr>
                        <tr><td>2</td><td>Bob</td><td>Baker</td><td>bob.baker@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2023-02-03</td></tr>
                        <tr><td>3</td><td>Carol</td><td>Clark</td><td>carol.clark@example.com</td><td>Viewer</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2023-02-18</td></tr>
                        <tr><td>4</td><td>David</td><td>Davis</td><td>david.davis@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2023-03-07</td></tr>
                        <tr><td>5</td><td>Eve</td><td>Evans</td><td>eve.evans@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2023-03-22</td></tr>
                        <tr><td>6</td><td>Frank</td><td>Foster</td><td>frank.foster@example.com</td><td>Editor</td><td><span class="badge text-bg-danger">Banned</span></td><td>2023-04-01</td></tr>
                        <tr><td>7</td><td>Grace</td><td>Green</td><td>grace.green@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2023-04-14</td></tr>
                        <tr><td>8</td><td>Henry</td><td>Harris</td><td>henry.harris@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2023-05-09</td></tr>
                        <tr><td>9</td><td>Isla</td><td>Ingram</td><td>isla.ingram@example.com</td><td>Editor</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2023-05-25</td></tr>
                        <tr><td>10</td><td>Jack</td><td>Jones</td><td>jack.jones@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2023-06-02</td></tr>
                        <tr><td>11</td><td>Karen</td><td>King</td><td>karen.king@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2023-06-18</td></tr>
                        <tr><td>12</td><td>Liam</td><td>Lee</td><td>liam.lee@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2023-07-04</td></tr>
                        <tr><td>13</td><td>Mia</td><td>Moore</td><td>mia.moore@example.com</td><td>Viewer</td><td><span class="badge text-bg-danger">Banned</span></td><td>2023-07-19</td></tr>
                        <tr><td>14</td><td>Noah</td><td>Nelson</td><td>noah.nelson@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2023-08-05</td></tr>
                        <tr><td>15</td><td>Olivia</td><td>Owen</td><td>olivia.owen@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2023-08-20</td></tr>
                        <tr><td>16</td><td>Peter</td><td>Parker</td><td>peter.parker@example.com</td><td>Editor</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2023-09-01</td></tr>
                        <tr><td>17</td><td>Quinn</td><td>Quinn</td><td>quinn.quinn@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2023-09-14</td></tr>
                        <tr><td>18</td><td>Rachel</td><td>Reed</td><td>rachel.reed@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2023-09-28</td></tr>
                        <tr><td>19</td><td>Sam</td><td>Scott</td><td>sam.scott@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2023-10-10</td></tr>
                        <tr><td>20</td><td>Tina</td><td>Turner</td><td>tina.turner@example.com</td><td>Viewer</td><td><span class="badge text-bg-danger">Banned</span></td><td>2023-10-22</td></tr>
                        <tr><td>21</td><td>Uma</td><td>Underwood</td><td>uma.underwood@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2023-11-03</td></tr>
                        <tr><td>22</td><td>Victor</td><td>Vance</td><td>victor.vance@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2023-11-17</td></tr>
                        <tr><td>23</td><td>Wendy</td><td>Walsh</td><td>wendy.walsh@example.com</td><td>Admin</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2023-12-01</td></tr>
                        <tr><td>24</td><td>Xander</td><td>Xu</td><td>xander.xu@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2023-12-15</td></tr>
                        <tr><td>25</td><td>Yara</td><td>Young</td><td>yara.young@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-01-07</td></tr>
                        <tr><td>26</td><td>Zoe</td><td>Zhang</td><td>zoe.zhang@example.com</td><td>Editor</td><td><span class="badge text-bg-danger">Banned</span></td><td>2024-01-20</td></tr>
                        <tr><td>27</td><td>Aaron</td><td>Adams</td><td>aaron.adams@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-02-04</td></tr>
                        <tr><td>28</td><td>Beth</td><td>Brown</td><td>beth.brown@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2024-02-18</td></tr>
                        <tr><td>29</td><td>Chris</td><td>Cooper</td><td>chris.cooper@example.com</td><td>Admin</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2024-03-03</td></tr>
                        <tr><td>30</td><td>Diana</td><td>Diaz</td><td>diana.diaz@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-03-17</td></tr>
                        <tr><td>31</td><td>Ethan</td><td>Ellis</td><td>ethan.ellis@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2024-04-01</td></tr>
                        <tr><td>32</td><td>Fiona</td><td>Flynn</td><td>fiona.flynn@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-04-14</td></tr>
                        <tr><td>33</td><td>George</td><td>Grant</td><td>george.grant@example.com</td><td>Editor</td><td><span class="badge text-bg-danger">Banned</span></td><td>2024-04-28</td></tr>
                        <tr><td>34</td><td>Hannah</td><td>Hill</td><td>hannah.hill@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2024-05-10</td></tr>
                        <tr><td>35</td><td>Ian</td><td>Irving</td><td>ian.irving@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-05-24</td></tr>
                        <tr><td>36</td><td>Julia</td><td>James</td><td>julia.james@example.com</td><td>Editor</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2024-06-07</td></tr>
                        <tr><td>37</td><td>Kyle</td><td>Knight</td><td>kyle.knight@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-06-21</td></tr>
                        <tr><td>38</td><td>Laura</td><td>Lane</td><td>laura.lane@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2024-07-05</td></tr>
                        <tr><td>39</td><td>Mike</td><td>Mills</td><td>mike.mills@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2024-07-19</td></tr>
                        <tr><td>40</td><td>Nina</td><td>Nash</td><td>nina.nash@example.com</td><td>Viewer</td><td><span class="badge text-bg-danger">Banned</span></td><td>2024-08-02</td></tr>
                        <tr><td>41</td><td>Oscar</td><td>Olsen</td><td>oscar.olsen@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2024-08-16</td></tr>
                        <tr><td>42</td><td>Paula</td><td>Price</td><td>paula.price@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-08-30</td></tr>
                        <tr><td>43</td><td>Quentin</td><td>Quincy</td><td>quentin.quincy@example.com</td><td>Editor</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2024-09-13</td></tr>
                        <tr><td>44</td><td>Rosa</td><td>Rivera</td><td>rosa.rivera@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2024-09-27</td></tr>
                        <tr><td>45</td><td>Steve</td><td>Stone</td><td>steve.stone@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2024-10-11</td></tr>
                        <tr><td>46</td><td>Tracy</td><td>Todd</td><td>tracy.todd@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2024-10-25</td></tr>
                        <tr><td>47</td><td>Ursula</td><td>Upton</td><td>ursula.upton@example.com</td><td>Viewer</td><td><span class="badge text-bg-danger">Banned</span></td><td>2024-11-08</td></tr>
                        <tr><td>48</td><td>Vince</td><td>Vaughn</td><td>vince.vaughn@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2024-11-22</td></tr>
                        <tr><td>49</td><td>Wilma</td><td>Ward</td><td>wilma.ward@example.com</td><td>Admin</td><td><span class="badge text-bg-success">Active</span></td><td>2024-12-06</td></tr>
                        <tr><td>50</td><td>Xena</td><td>Xavier</td><td>xena.xavier@example.com</td><td>Viewer</td><td><span class="badge text-bg-warning">Inactive</span></td><td>2024-12-20</td></tr>
                        <tr><td>51</td><td>Yusuf</td><td>York</td><td>yusuf.york@example.com</td><td>Editor</td><td><span class="badge text-bg-success">Active</span></td><td>2025-01-03</td></tr>
                        <tr><td>52</td><td>Zara</td><td>Zimmerman</td><td>zara.zimmerman@example.com</td><td>Viewer</td><td><span class="badge text-bg-success">Active</span></td><td>2025-01-17</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>