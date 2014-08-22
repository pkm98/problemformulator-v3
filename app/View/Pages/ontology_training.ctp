<div class="row-fluid">
    <div class="span10 offset1">
        <h2>Training in the Problem Map ontology</h2>

        <img class='pull-right thumbnail' style="width:400px;" 
        src="../img/problem-map-chris.png">

        <p>During this study, we would like you to brainstorm your ideas within
        the problem map ontology. This ontology makes a distinction between four
        categories of ideas.</p>

        <p>The first group is composed of requirements, which are the
        objectives, goals, and constraints that must be achieved in the final
        design. Many of these ideas usually come directly from the design brief;
        these ideas might be the information about the user who will be using
        the Requirements might also contain information about the context in
        which the final design will be used, which may impose additional
        constraints.</p>

        <p>The second group is composed of functions, or the actions and
        procedures that might be a part of your final design. These ideas are
        almost always embodied as verb-noun pairs (e.g., sends email).</p>

        <p>The third group is composed of artifacts, or the objects and
        components that might be used in the final design. These ideas are
        embodied as nouns (e.g., the home page). This group stores all of the
        structural ideas related to the design—it might contain the pages,
        widgets, and content of the final design.</p>

        <p>The final group is composed of behaviors, which in the case of user
        interface design correspond to the interaction techniques used in the
        design (e.g., touch, swipe, or hold interactions) as well as the
        parameters of these interactions (e.g., hold for 5 seconds).</p>

        <p>Each of these groups is organized hierarchically, so ideas can
        decompose into more granular ideas. For example, the send email function
        might decompose into selecting a recipient to the email, typing the
        content of the email, and actually sending it.</p>

        <p>Finally, entities from one group can be associated with entities of
        another group. So a requirement that the application allow for
        communication between individuals of the group might be associated with
        the send email function, asserting that this function satisfies the
        requirement. Likewise, entities of any group can be linked with entities
        of any other group.</p>

        <p>To ensure that you understand this ontology please categorize the
        following ideas into one of these four groups:</p>

        <h2>Practice Problems</h2>

        <table class='table'>
            <thead>
                <tr>
                    <th>Idea</th>
                    <th>Label</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Submit movie rating</td>
                    <td>
                        <select id='1'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Must allow users to locate a movie review</td>
                    <td>
                        <select id='2'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Home page</td>
                    <td>
                        <select id='3'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Shake phone to activate</td>
                    <td>
                        <select id='4'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Sort reviews</td>
                    <td>
                        <select id='5'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Research showed that the simplicity of the application
                        is extremely important to users</td>
                    <td>
                        <select id='6'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>"Like" button</td>
                    <td>
                        <select id='7'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Use location to trigger the function</td>
                    <td>
                        <select id='8'>
                            <option></option>
                            <option>Requirement</option>
                            <option>Function</option>
                            <option>Artifact</option>
                            <option>Behavior</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <button id="next" class='btn btn-primary' style='margin-bottom:100px'>Continue</button>

        <script>
            $('#next').click(function(){

                var error = false;

                if ($('#1').val() != 'Function'){
                    console.log('error')
                    error = true;
                }
                if ($('#2').val() != 'Requirement'){
                    console.log('error')
                    error = true;
                }
                if ($('#3').val() != 'Artifact'){
                    console.log('error')
                    error = true;
                }
                if ($('#4').val() != 'Behavior'){
                    console.log('error')
                    error = true;
                }
                if ($('#5').val() != 'Function'){
                    console.log('error')
                    error = true;
                }
                if ($('#6').val() != 'Requirement'){
                    console.log('error')
                    error = true;
                }
                if ($('#7').val() != 'Artifact'){
                    console.log('error')
                    error = true;
                }
                if ($('#8').val() != 'Behavior'){
                    console.log('error')
                    error = true;
                }
                
                if (!error){
                    console.log('good');
                }

            });

        </script>
        
    </div>
</div>
