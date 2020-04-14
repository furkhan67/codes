Public Class Form4

    Private Sub Button2_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles Button2.Click

    End Sub

    Private Sub Button3_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles Button3.Click

    End Sub

    Private Sub Button5_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles Button5.Click

    End Sub

    Private Sub Button1_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles Button1.Click

    End Sub

    Private Sub Form4_Load(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles MyBase.Load
        'TODO: This line of code loads data into the 'Parking1DataSet.Fourwheeler' table. You can move, or remove it, as needed.
        Me.FourwheelerTableAdapter.Fill(Me.Parking1DataSet.Fourwheeler)
        'TODO: This line of code loads data into the 'ParkingDataSet.twheeler' table. You can move, or remove it, as needed.
        Me.TwheelerTableAdapter.Fill(Me.ParkingDataSet.twheeler)
        'TODO: This line of code loads data into the 'PakingDataSet1.three' table. You can move, or remove it, as needed.
        Me.ThreeTableAdapter.Fill(Me.PakingDataSet1.three)

    End Sub
End Class