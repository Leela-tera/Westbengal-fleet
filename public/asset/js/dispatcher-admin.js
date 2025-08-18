'use strict';

class DispatcherPanel extends React.Component {
    componentWillMount() {
        this.setState({
            listContent: 'dispatch-map'
        });
    }

    handleUpdateBody(body) {
        console.log('Body Update Called', body);
        this.setState({
            listContent: body
        });
    }

    handleUpdateFilter(filter) {
        console.log('Filter Update Called', this.state.listContent);
        if(filter == 'all'){
            this.setState({
                listContent: 'dispatch-map'
            });
        }else if(filter == 'cancelled'){
            this.setState({
                listContent: 'dispatch-cancelled'
            });
        }else if(filter == 'ongoing'){
            this.setState({
                listContent: 'dispatch-ongoing'
            });
        }else if(filter == 'return'){
            this.setState({
                listContent: 'dispatch-return'
            });
        }else if(filter == 'reassigned'){
            this.setState({
                listContent: 'dispatch-reassign'
            });
        }else if(filter == 'completed'){
          this.setState({
                listContent: 'dispatch-completed'
            });
        }
        else{
            this.setState({
                listContent: 'dispatch-map'
            });
        }
    }

    handleRequestShow(trip) {
        // console.log('Show Request', trip);
        if(trip.current_provider_id == 0) {
            this.setState({
                listContent: 'dispatch-assign',
                trip: trip
            });
        } else {
            this.setState({
                listContent: 'dispatch-map',
                trip: trip
            });
        }
        ongoingInitialize(trip);
    }
    handleOngoingRequestShow(trip) {
         
        this.setState({
            listContent: 'dispatch-ongoing',
            trip: trip
        });
        
        ongoingtrackproviderInitialize(trip);
    }

    handleRequestCancel(argument) {
        this.setState({
            listContent: 'dispatch-map'
        });
    }

    handleRequestReassign(argument) {
        this.setState({
            listContent: 'dispatch-reassign'
        });
    }

     handleRequestComplete(argument) {
        this.setState({
            listContent: 'dispatch-completed'
        });
    }

    handleRequestCompleteShow(trip) {
         
        this.setState({
            listContent: 'dispatch-completed',
            trip: trip
        });
        
        ongoingtrackproviderInitialize(trip);
    }


    render() {

        let listContent = null;

        // console.log('DispatcherPanel', this.state.listContent);

        switch(this.state.listContent) {
            case 'dispatch-create':
                listContent = <div className="col-md-4">
                        <DispatcherRequest completed={this.handleRequestShow.bind(this)} cancel={this.handleRequestCancel.bind(this)} />
                    </div>;
                break;
            case 'dispatch-map':
                listContent = <div className="col-md-4">
                        <DispatcherList clicked={this.handleRequestShow.bind(this)} />
                    </div>;
                break;
            case 'dispatch-cancelled':
                listContent = <div className="col-md-4">
                        <DispatcherCancelledList />
                    </div>;
                break; 
            case 'dispatch-ongoing':
                listContent = <div className="col-md-4">
                        <DispatcherOngoingList clicked={this.handleOngoingRequestShow.bind(this)}/>
                    </div>;
                break;
            case 'dispatch-assign':
                listContent = <div className="col-md-4">
                        <DispatcherAssignList trip={this.state.trip} />
                    </div>;
                break;
             case 'dispatch-reassign':
                listContent = <div className="col-md-4">
                        <DispatcherReAssignList />
                    </div>;
                break; 

             case 'dispatch-completed':
                listContent = <div className="col-md-4">
                        <DispatcherCompletedList clicked={this.handleRequestCompleteShow.bind(this)}/>
                    </div>;
                break;         
        }

        return (
            <div className="container-fluid">
                <h4>Dispatcher</h4>

                <DispatcherNavbar body={this.state.listContent} updateBody={this.handleUpdateBody.bind(this)} updateFilter={this.handleUpdateFilter.bind(this)}/>

                <div className="row">
                    { listContent }

                    <div className="col-md-8">
                        <DispatcherMap body={this.state.listContent} />
                    </div>
                </div>
            </div>
        );

    }
};

class DispatcherNavbar extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            body: 'dispatch-map',
            selected:''
        };
    }

    filter(data) {
        console.log('Navbar Filter', data);
        this.setState({selected  : data})
        this.props.updateFilter(data);
    }

    handleBodyChange() {
        // console.log('handleBodyChange', this.state);
        if(this.props.body != this.state.body) {
            this.setState({
                body: this.props.body
            });
        }

        if(this.state.body == 'dispatch-map') {
            this.props.updateBody('dispatch-create');
            this.setState({
                body: 'dispatch-create'
            });
        }else if(this.state.body == 'dispatch-cancelled') {
            this.props.updateBody('dispatch-map');
            this.setState({
                body: 'dispatch-cancelled'
            });
        } else if(this.state.body == 'dispatch-reassign') {
            this.props.updateBody('dispatch-reassign');
            this.setState({
                body: 'dispatch-reassign'
            });
        } else if(this.state.body == 'dispatch-completed'){
          this.props.updateBody('dispatch-completed');
            this.setState({
                body: 'dispatch-completed'
            });
         }else {
            this.props.updateBody('dispatch-map');
            this.setState({
                body: 'dispatch-map'
            });
        }
    }

    isActive(value){
        return 'nav-item '+((value===this.state.selected) ?'active':'');
    }

    render() {
        return (
            <nav className="navbar navbar-light bg-white b-a mb-2">
                <button className="navbar-toggler hidden-md-up" 
                    data-toggle="collapse"
                    data-target="#process-filters"
                    aria-controls="process-filters"
                    aria-expanded="false"
                    aria-label="Toggle Navigation"></button>
                

                

                <div className="collapse navbar-toggleable-sm" id="process-filters">
                    <ul className="nav navbar-nav dispatcher-nav">
                        <li className={this.isActive('all')} onClick={this.filter.bind(this, 'all')}>
                            <span className="nav-link" href="#">Open</span>
                        </li>

                        <li className={this.isActive('cancelled')} onClick={this.filter.bind(this, 'cancelled')}>
                            <span className="nav-link" href="#">Cancelled</span>
                        </li>
                         <li className={this.isActive('reassigned')} onClick={this.filter.bind(this, 'reassigned')}>
                            <span className="nav-link" href="#">Reassignment</span>
                        </li> 
                        <li className={this.isActive('ongoing')} onClick={this.filter.bind(this, 'ongoing')}>
                            <span className="nav-link" href="#">Ongoing</span>
                        </li>
                        <li className={this.isActive('completed')} onClick={this.filter.bind(this, 'completed')}>
                            <span className="nav-link" href="#">Completed</span>
                        </li>
                        
                    </ul>
                </div>
            </nav>
        );
    }
}

class DispatcherList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: {
                data: []
            }
        };
    }

    componentDidMount() {
        window.worldMapInitialize();
        window.Tranxit.TripTimer = setInterval(
            () => this.getTripsUpdate(),
            1000
        );
    }

    componentWillUnmount() {
        clearInterval(window.Tranxit.TripTimer);
    }

    getTripsUpdate() {
        $.get('/admin/dispatcher/trips?type=SEARCHING', function(result) {
            if(result.hasOwnProperty('data')) {
                this.setState({
                    data: result
                });
            } else {
                this.setState({
                    data: {
                        data: []
                    }
                });
            }
        }.bind(this));
    }

    handleClick(trip) {
        this.props.clicked(trip);
    }

    render() {
        return (
            <div className="card">
                <div className="card-header text-uppercase"><b>Searching List</b></div>
                <DispatcherListItem data={this.state.data.data} clicked={this.handleClick.bind(this)} />
            </div>
        );
    }
}

class DispatcherOngoingList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: {
                data: []
            }
        };
    }

    componentDidMount() {
        window.worldMapInitialize();
        window.Tranxit.TripTimer = setInterval(
            () => this.getTripsUpdate(),
            1000
        );
    }

    componentWillUnmount() {
        clearInterval(window.Tranxit.TripTimer);
    }

    getTripsUpdate() {
        $.get('/admin/dispatcher/trips?type=PICKEDUP', function(result) {
            if(result.hasOwnProperty('data')) {
                this.setState({
                    data: result
                });
            } else {
                this.setState({
                    data: {
                        data: []
                    }
                });
            }
        }.bind(this));
    }

    handleClick(trip) {
        this.props.clicked(trip);
    }

    render() {
        return (
            <div className="card">
                <div className="card-header text-uppercase"><b>Ongoing List</b></div>
                <DispatcherOngoingListItem data={this.state.data.data} clicked={this.handleClick.bind(this)} />
            </div>
        );
    }
}

class DispatcherCompletedList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: {
                data: []
            }
        };
    }

    componentDidMount() {
        window.worldMapInitialize();
        window.Tranxit.TripTimer = setInterval(
            () => this.getTripsUpdate(),
            1000
        );
    }

    componentWillUnmount() {
        clearInterval(window.Tranxit.TripTimer);
    }

    getTripsUpdate() {
        $.get('/admin/dispatcher/trips?type=COMPLETED', function(result) {
            if(result.hasOwnProperty('data')) {
                this.setState({
                    data: result
                });
            } else {
                this.setState({
                    data: {
                        data: []
                    }
                });
            }
        }.bind(this));
    }

    handleClick(trip) {
        this.props.clicked(trip);
    }

    render() {
        return (
            <div className="card">
                <div className="card-header text-uppercase"><b>Completed List</b></div>
                <DispatcherCompletedListItem data={this.state.data.data} clicked={this.handleClick.bind(this)} />
            </div>
        );
    }
}


class DispatcherCancelledList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: {
                data: []
            }
        };
    }

    componentDidMount() {
        window.worldMapInitialize();
        window.Tranxit.TripTimer = setInterval(
            () => this.getTripsUpdate(),
            1000
        );
    }

    componentWillUnmount() {
        clearInterval(window.Tranxit.TripTimer);
    }

    getTripsUpdate() {
        $.get('/admin/dispatcher/trips?type=CANCELLED', function(result) {
            if(result.hasOwnProperty('data')) {
                this.setState({
                    data: result
                });
            } else {
                this.setState({
                    data: {
                        data: []
                    }
                });
            }
        }.bind(this));
    }

    render() {
        return (
            <div className="card">
                <div className="card-header text-uppercase"><b>Cancelled List</b></div>
                <DispatcherCancelledListItem data={this.state.data.data} />
            </div>
        );
    }
}


class DispatcherCancelledListItem extends React.Component {

    render() {
        var listItem = function(trip) {
            return (
                    <div className="il-item" key={trip.id}>
                        <a className="text-black" href="#">
                            <div className="media">
                                <div className="media-body">
                                    <div className="row">
                                      <div className="col-md-5">
                                        <h5> {trip.downreason} </h5>
                                      </div>
                                      <div className="col-md-7">
                                        {trip.status == 'COMPLETED' ?
                                            <span className="tag tag-success pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'CANCELLED' ?
                                            <span className="tag tag-danger pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SEARCHING' ?
                                            <span className="tag tag-warning pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SCHEDULED' ?
                                            <span className="tag tag-primary pull-right tag-brp"> {trip.status} </span>
                                        : 
                                        <span className="tag tag-info pull-right tag-brp"> {trip.status} </span>
                                        }
                                      </div>
                                    </div>
                                    <hr />
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>From: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.s_address}</p>
                                      </div>
                                    </div>
                                    <div className="media mb-2">
                                      <div className="media-left media-top wd-5">
                                        <p>To: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.d_address ? trip.d_address : "Not Selected"}</p>
                                      </div>
                                    </div>
                                    <progress className="progress progress-success progress-sm" max="100"></progress>
                                    <span className="text-muted">Cancelled at : {trip.updated_at}</span>
                                </div>
                            </div>
                        </a>
                        <a className="btn btn-danger tag-brp" href={"/admin/dispatcher/assignform/"+ trip.id} >Assign</a>
                    </div>
                );
        }.bind(this);

        return (
            <div className="items-list">
                {this.props.data.map(listItem)}
            </div>
        );
    }
}


class DispatcherReAssignList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: {
                data: []
            }
        };
    }

    componentDidMount() {
        window.worldMapInitialize();
        window.Tranxit.TripTimer = setInterval(
            () => this.getTripsUpdate(),
            1000
        );
    }

    componentWillUnmount() {
        clearInterval(window.Tranxit.TripTimer);
    }

    getTripsUpdate() {
        $.get('/admin/dispatcher/trips?type=REASSIGNED', function(result) {
            if(result.hasOwnProperty('data')) {
                this.setState({
                    data: result
                });
            } else {
                this.setState({
                    data: {
                        data: []
                    }
                });
            }
        }.bind(this));
    }

    handleClick(trip) {
        this.props.clicked(trip);
    }

    render() {
        return (
            <div className="card">
                <div className="card-header text-uppercase"><b>Reassigned List</b></div>
                <DispatcherReAssignListItem data={this.state.data.data} clicked={this.handleClick.bind(this)} />
            </div>
        );
    }
}

class DispatcherListItem extends React.Component {
    handleClick(trip) {
        this.props.clicked(trip)
    }
    render() {
        var listItem = function(trip) {
            return (
                    <div className="il-item" key={trip.id} onClick={this.handleClick.bind(this, trip)}>
                        <a className="text-black" href="#">
                            <div className="media">
                                <div className="media-body">
                                    <div className="row">
                                      <div className="col-md-5">
                                        <h5> Ticket Id : </h5>
                                        <p className="text-muted "> {trip.booking_id} </p>
                                      </div>
                                      <div className="col-md-7">
                                        {trip.status == 'COMPLETED' ?
                                            <span className="tag tag-success pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'CANCELLED' ?
                                            <span className="tag tag-danger pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SEARCHING' ?
                                            <span className="tag tag-warning pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SCHEDULED' ?
                                            <span className="tag tag-primary pull-right tag-brp"> {trip.status} </span>
                                        : 
                                        <span className="tag tag-info pull-right tag-brp"> {trip.status} </span>
                                        }
                                      </div>
                                    </div>
                                    <hr />
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>Type: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.downreason}</p>
                                      </div>
                                    </div>
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>From: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.s_address}</p>
                                      </div>
                                    </div>
                                    <div className="media mb-2">
                                      <div className="media-left media-top wd-5">
                                        <p>To: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.d_address ? trip.d_address : "Not Selected"}</p>
                                      </div>
                                    </div>
                                    <progress className="progress progress-success progress-sm" max="100"></progress>
                                    <p className="text-muted">{trip.current_provider_id == 0 ? "Manual Assignment" : "Auto Search"} : {trip.created_at}</p>
                                </div>
                            </div>
                        </a>
                        <a className="btn btn-primary btn-blu tag-brp" href={"/admin/requests/" + trip.id} >More Details</a> &nbsp;
                        <a className="btn btn-danger tag-brp" href={"/admin/dispatcher/cancel?request_id=" + trip.id} >Cancel</a>
                    </div>
                );
        }.bind(this);

        return (
            <div className="items-list">
                {this.props.data.map(listItem)}
            </div>
        );
    }
}

class DispatcherReAssignListItem extends React.Component {
    handleClick(trip) {
        this.props.clicked(trip)
    }
    render() {
        var listItem = function(trip) {
            return (
                    <div className="il-item" key={trip.id} onClick={this.handleClick.bind(this, trip)}>
					  <a className="text-black" href="#">
                        <div className="media">
                            <div className="media-body">
                                <div className="row">
                                  <div className="col-md-5">
                                    <h5> Ticket Id : </h5>
                                    <p className="text-muted "> {trip.booking_id} </p>
                                  </div>
                                  <div className="col-md-7">
                                    {trip.status == 'COMPLETED' ?
                                        <span className="tag tag-success pull-right tag-brp"> {trip.status} </span>
                                    : trip.status == 'CANCELLED' ?
                                        <span className="tag tag-danger pull-right tag-brp"> {trip.status} </span>
                                    : trip.status == 'SEARCHING' ?
                                        <span className="tag tag-warning pull-right tag-brp"> {trip.status} </span>
                                    : trip.status == 'SCHEDULED' ?
                                        <span className="tag tag-primary pull-right tag-brp"> {trip.status} </span>
                                    : 
                                    <span className="tag tag-info pull-right tag-brp"> {trip.status} </span>
                                    }
                                  </div>
                                </div>
                                <hr />
                                <div className="media">
                                  <div className="media-left media-top wd-5">
                                    <p>Type: </p>
                                  </div>
                                  <div className="media-body">
                                    <p>{trip.downreason}</p>
                                  </div>
                                </div>
                                <div className="media">
                                  <div className="media-left media-top wd-5">
                                    <p>From: </p>
                                  </div>
                                  <div className="media-body">
                                    <p>{trip.s_address}</p>
                                  </div>
                                </div>
                                <div className="media mb-2">
                                  <div className="media-left media-top wd-5">
                                    <p>To: </p>
                                  </div>
                                  <div className="media-body">
                                    <p>{trip.d_address ? trip.d_address : "Not Selected"}</p>
                                  </div>
                                </div>
                                <progress className="progress progress-success progress-sm" max="100"></progress>
                                <span className="text-muted">{trip.current_provider_id == 0 ? "Manual Assignment" : "Auto Search"} : {trip.created_at}</span>
                            </div>
                        </div>
                        </a>
                        <a className="btn btn-primary btn-blu tag-brp" href={"/admin/requests/" + trip.id} >More Details</a> &nbsp;
                        <a className="btn btn-danger  tag-brp" href={"/admin/dispatcher/assignform/"+ trip.id} >Assign</a> 
                      
                    </div>
                );
        }.bind(this);

        return (
            <div className="items-list">
                {this.props.data.map(listItem)}
            </div>
        );
    }
}
class DispatcherOngoingListItem extends React.Component {
    handleClick(trip) {
        this.props.clicked(trip)
    }
    render() {
        var listItem = function(trip) {
            return (
                    <div className="il-item" key={trip.id} onClick={this.handleClick.bind(this, trip)}>
                        <a className="text-black" href="#">
                            <div className="media">
                                <div className="media-body">
                                    <div className="row">
                                      <div className="col-md-5">
                                        <h5> Ticket Id : </h5>
                                        <p className="text-muted "> {trip.booking_id} </p>
                                      </div>
                                      <div className="col-md-7">
                                        {trip.status == 'COMPLETED' ?
                                            <span className="tag tag-success pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'CANCELLED' ?
                                            <span className="tag tag-danger pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SEARCHING' ?
                                            <span className="tag tag-warning pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SCHEDULED' ?
                                            <span className="tag tag-primary pull-right tag-brp"> {trip.status} </span>
                                        : 
                                        <span className="tag tag-info pull-right tag-brp"> {trip.status} </span>
                                        }
                                      </div>
                                    </div>
                                    <hr />
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>Type: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.downreason}</p>
                                      </div>
                                    </div>
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>From: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.s_address}</p>
                                      </div>
                                    </div>
                                    <div className="media mb-2">
                                      <div className="media-left media-top wd-5">
                                        <p>To: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.d_address ? trip.d_address : "Not Selected"}</p>
                                      </div>
                                    </div>
                                    <progress className="progress progress-success progress-sm" max="100"></progress>
                                    <p className="text-muted">{trip.current_provider_id == 0 ? "Manual Assignment" : "Auto Search"} : {trip.created_at}</p>
                                </div>
                            </div>
                        </a>
                        <a className="btn btn-primary btn-blu tag-brp" href={"/admin/requests/" + trip.id} >More Details</a>
                    </div>
                );
        }.bind(this);

        return (
            <div className="items-list">
                {this.props.data.map(listItem)}
            </div>
        );
    }
}


class DispatcherCompletedListItem extends React.Component {
    handleClick(trip) {
        this.props.clicked(trip)
    }
    render() {
        var listItem = function(trip) {
            return (
                    <div className="il-item" key={trip.id} onClick={this.handleClick.bind(this, trip)}>
                        <a className="text-black" href="#">
                            <div className="media">
                                <div className="media-body">
                                    <div className="row">
                                      <div className="col-md-5">
                                        <h5> Ticket Id : </h5>
                                        <p className="text-muted "> {trip.booking_id} </p>
                                      </div>
                                      <div className="col-md-7">
                                        {trip.status == 'COMPLETED' ?
                                            <span className="tag tag-success pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'CANCELLED' ?
                                            <span className="tag tag-danger pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SEARCHING' ?
                                            <span className="tag tag-warning pull-right tag-brp"> {trip.status} </span>
                                        : trip.status == 'SCHEDULED' ?
                                            <span className="tag tag-primary pull-right tag-brp"> {trip.status} </span>
                                        : 
                                            <span className="tag tag-info pull-right tag-brp"> {trip.status} </span>
                                        }
                                      </div>
                                    </div>
                                    <hr />
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>Type: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.downreason}</p>
                                      </div>
                                    </div>
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>From: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.s_address}</p>
                                      </div>
                                    </div>
                                    <div className="media mb-2">
                                      <div className="media-left media-top wd-5">
                                        <p>To: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{trip.d_address ? trip.d_address : "Not Selected"}</p>
                                      </div>
                                    </div>
                                    <progress className="progress progress-success progress-sm" max="100"></progress>
                                    <p className="text-muted">{trip.current_provider_id == 0 ? "Manual Assignment" : "Auto Search"} : {trip.created_at}</p>
                                </div>
                            </div>
                        </a>
                        <a className="btn btn-primary btn-blu tag-brp" href={"/admin/requests/" + trip.id} >More Details</a>
                    </div>
                );
        }.bind(this);

        return (
            <div className="items-list">
                {this.props.data.map(listItem)}
            </div>
        );
    }
}

class DispatcherRequest extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            data: []
        };
    }

    componentDidMount() {

        // Auto Assign Switch
        new Switchery(document.getElementById('provider_auto_assign'));
        
        // Schedule Time Datepicker
        $('#schedule_time').datetimepicker({
            minDate: window.Tranxit.minDate,
            maxDate: window.Tranxit.maxDate,
        });

        // Get Service Type List
        $.get('/admin/service', function(result) {
            this.setState({
                data: result
            });
        }.bind(this));

        // Mount Ride Create Map

        window.createRideInitialize();

        function stopRKey(evt) { 
            var evt = (evt) ? evt : ((event) ? event : null); 
            var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
            if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
        } 

        document.onkeypress = stopRKey; 
    }

    createRide(event) {
        console.log(event);
        event.preventDefault();
        event.stopPropagation();
        console.log('Hello', $("#form-create-ride").serialize());
        $.ajax({
            url: '/admin/dispatcher',
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            type: 'POST',
            data: $("#form-create-ride").serialize(),
            success: function(data) {
                console.log('Accept', data);
                this.props.completed(data);
            }.bind(this)
        });
    }

    cancelCreate() {
        this.props.cancel(true);
    }

    render() {
        return (
            <div className="card card-block" id="create-ride">
                <h3 className="card-title text-uppercase">Ride Details</h3>
                <form id="form-create-ride" onSubmit={this.createRide.bind(this)} method="POST">
                    <div className="row">
                        <div className="col-xs-6">
                            <div className="form-group">
                                <label htmlFor="first_name">First Name</label>
                                <input type="text" className="form-control" name="first_name" id="first_name" placeholder="First Name" required />
                            </div>
                        </div>
                        <div className="col-xs-6">
                            <div className="form-group">
                                <label htmlFor="last_name">Last Name</label>
                                <input type="text" className="form-control" name="last_name" id="last_name" placeholder="Last Name" required />
                            </div>
                        </div>
                        <div className="col-xs-6">
                            <div className="form-group">
                                <label htmlFor="email">Email</label>
                                <input type="email" className="form-control" name="email" id="email" placeholder="Email" required/>
                            </div>
                        </div>
                        <div className="col-xs-6">
                            <div className="form-group">
                                <label htmlFor="mobile">Phone</label>
                                <input type="text" className="form-control" name="mobile" id="mobile" placeholder="Phone" required />
                            </div>
                        </div>
                        <div className="col-xs-12">
                            <div className="form-group">
                                <label htmlFor="s_address">Pickup Address</label>
                                
                                <input type="text"
                                    name="s_address"
                                    className="form-control"
                                    id="s_address"
                                    placeholder="Pickup Address"
                                    required></input>

                                <input type="hidden" name="s_latitude" id="s_latitude"></input>
                                <input type="hidden" name="s_longitude" id="s_longitude"></input>
                            </div>
                            <div className="form-group">
                                <label htmlFor="d_address">Dropoff Address</label>
                                
                                <input type="text" 
                                    name="d_address"
                                    className="form-control"
                                    id="d_address"
                                    placeholder="Dropoff Address"
                                    required></input>

                                <input type="hidden" name="d_latitude" id="d_latitude"></input>
                                <input type="hidden" name="d_longitude" id="d_longitude"></input>
                                <input type="hidden" name="distance" id="distance"></input>
                            </div>
                            {/*<div className="form-group">
                                <label htmlFor="schedule_time">Schedule Time</label>
                                <input type="text" className="form-control" name="schedule_time" id="schedule_time" placeholder="Date" required/>
                            </div>*/}
                            <div className="form-group">
                                <label htmlFor="service_types">Service Type</label>
                                <ServiceTypes data={this.state.data} />
                            </div>
                            <div className="form-group">
                                <label htmlFor="provider_auto_assign">Manual Assign Provider</label> 
                                <input type="checkbox" id="provider_auto_assign" name="provider_auto_assign" className="js-switch" data-color="#f59345"/>
                                <label htmlFor="provider_auto_assign">Auto Assign Provider</label> 
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xs-6">
                            <button type="button" className="btn btn-lg btn-danger btn-block waves-effect waves-light" onClick={this.cancelCreate.bind(this)}>
                                CANCEL
                            </button>
                        </div>
                        <div className="col-xs-6">
                            <button className="btn btn-lg btn-success btn-block waves-effect waves-light">
                                SUBMIT
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        );
    }
};

class DispatcherAssignList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: {
                data: []
            }
        };
    }

    componentDidMount() {
        $.get('/admin/dispatcher/providers', { 
            service_type: this.props.trip.service_type_id,
            latitude: this.props.trip.s_latitude,
            longitude: this.props.trip.s_longitude
        }, function(result) {
            console.log('Providers', result);
            if(result) {
                result['data']=result;
                this.setState({
                    data: result
                });
                window.assignProviderShow(result.data, this.props.trip);
            } else {
                this.setState({
                    data: {
                        data: []
                    }
                });
                window.providerMarkersClear();
            }
        }.bind(this));
    }

    render() {
        console.log('DispatcherAssignList - render', this.state.data);
        return (
            <div className="card">
                <div className="card-header text-uppercase"><b>Assign Provider</b></div>
                
                <DispatcherAssignListItem data={this.state.data.data} trip={this.props.trip} />
            </div>
        );
    }
}

class DispatcherAssignListItem extends React.Component {
    handleClick(provider) {
        // this.props.clicked(trip)
        console.log('Provider Clicked');
        window.assignProviderPopPicked(provider);
    }
    render() {
        var listItem = function(provider) {
            return (
                    <div className="il-item" key={provider.id} onClick={this.handleClick.bind(this, provider)}>
                        <a className="text-black" href="#">
                            <div className="media">
                                <div className="media-body">
                                    <div className="row">
                                      <div className="col-md-5">
                                        <h5> {provider.first_name} {provider.last_name} </h5>
                                      </div>
                                      <div className="col-md-7">
                                        
                                      </div>
                                    </div>
                                    <hr />
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>Rating: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{provider.rating}</p>
                                      </div>
                                    </div>
                                    <div className="media">
                                      <div className="media-left media-top wd-5">
                                        <p>Phone: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{provider.mobile}</p>
                                      </div>
                                    </div>
                                    <div className="media mb-2">
                                      <div className="media-left media-top wd-5">
                                        <p>Type: </p>
                                      </div>
                                      <div className="media-body">
                                        <p>{provider.service.service_type.name}</p>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                );
        }.bind(this);

        return (
            <div className="items-list">
                {this.props.data.map(listItem)}
            </div>
        );
    }
}

class ServiceTypes extends React.Component {
    render() {
        // console.log('ServiceTypes', this.props.data);
        var mySelectOptions = function(result) {
            return <ServiceTypesOption
                    key={result.id}
                    id={result.id}
                    name={result.name} />
        };
        return (
                <select 
                    name="service_type"
                    className="form-control">
                    {this.props.data.map(mySelectOptions)}
                </select>
            )
    }
}

class ServiceTypesOption extends React.Component {
    render() {
        return (
            <option value={this.props.id}>{this.props.name}</option>
        );
    }
};

class DispatcherMap extends React.Component {
    render() {
        return (
            <div className="card my-card">
                <div className="card-header text-uppercase">
                    <b>MAP</b>
                </div>
                <div className="card-body">
                    <div id="map" style={{ height: '450px'}}></div>
                </div>
            </div>
        );
    }
}

ReactDOM.render(
    <DispatcherPanel />,
    document.getElementById('dispatcher-panel')
);